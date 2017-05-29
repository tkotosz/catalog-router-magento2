<?php

namespace Tkotosz\CatalogRouter\Observer;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;
use Tkotosz\CatalogRouter\Api\CatalogUrlPathProviderInterface;
use Tkotosz\CatalogRouter\Model\EntityData;
use Tkotosz\CatalogRouter\Model\Service\UrlPathUsedCheckerContainer;
use Tkotosz\CatalogRouter\Model\UrlPath;

abstract class PathValidatorObserver implements ObserverInterface
{
    /**
     * @var UrlPathUsedCheckerContainer
     */
    protected $urlPathUsedChecker;
    
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var CatalogUrlPathProviderInterface
     */
    protected $urlPathProvider;
    
    /**
     * @var UrlInterface
     */
    protected $urlProvider;
    
    /**
     * @var array
     */
    protected $entityEditUrls;
    
    /**
     * @param UrlPathUsedCheckerContainer     $urlPathUsedChecker
     * @param StoreManagerInterface           $storeManager
     * @param CatalogUrlPathProviderInterface $urlPathProvider
     * @param UrlInterface                    $urlProvider
     * @param array                           $entityEditUrls
     */
    public function __construct(
        UrlPathUsedCheckerContainer $urlPathUsedChecker,
        StoreManagerInterface $storeManager,
        CatalogUrlPathProviderInterface $urlPathProvider,
        UrlInterface $urlProvider,
        array $entityEditUrls
    ) {
        $this->urlPathUsedChecker = $urlPathUsedChecker;
        $this->storeManager = $storeManager;
        $this->urlPathProvider = $urlPathProvider;
        $this->urlProvider = $urlProvider;
        $this->entityEditUrls = $entityEditUrls;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $entity = $this->getEntity($observer);

        foreach ($this->getEntityStoreIds($entity) as $storeId) {
            $urlPath = $this->getEntityUrlPath($entity, $storeId);
            $resolvedEntities = $this->getOtherEntitiesWithPath($urlPath, $storeId, $entity);

            if (count($resolvedEntities) > 0) {
                throw new AlreadyExistsException($this->getErrorMessage($urlPath, $storeId, $resolvedEntities));
            }
        }
    }

    protected function getOtherEntitiesWithPath(UrlPath $urlPath, int $storeId, AbstractModel $currentEntity)
    {
        $resolvedEntities = [];

        $currentEntityData = new EntityData($this->getCurrentEntityType(), $currentEntity->getId(), $urlPath->getLastPart());

        foreach ($this->urlPathUsedChecker->check($urlPath, $storeId) as $entity) {
            if ($entity->getType() == $currentEntityData->getType() && $entity->getId() == $currentEntityData->getId()) {
                continue;
            }

            $resolvedEntities[] = $entity;
        }

        return $resolvedEntities;
    }

    protected function getErrorMessage(UrlPath $urlPath, int $storeId, array $entities)
    {
        $messages = [];

        $store = $this->storeManager->getStore($storeId);
        
        foreach ($entities as $entity) {
            $editUrlPath = $this->entityEditUrls[$entity->getType()]['url_path'];
            $idParam = $this->entityEditUrls[$entity->getType()]['id_param'];
            $editUrl = $this->urlProvider->getUrl($editUrlPath, [$idParam => $entity->getId()]);

            $messages[] = __(
                'The "%1" url path already used by a <a href="%2" target="_blank">%3 (id: %4)</a> in the %5 (id: %6) store',
                $urlPath->getFullPath(),
                $editUrl,
                $entity->getType(),
                $entity->getId(),
                $store->getName(),
                $store->getId()
            );
        }

        return __(join("<br>", $messages));
    }

    abstract protected function getCurrentEntityType();

    abstract protected function getEntity(Observer $observer);

    abstract protected function getEntityStoreIds(AbstractModel $entity);

    abstract protected function getEntityUrlPath(AbstractModel $entity, int $storeId);
}
