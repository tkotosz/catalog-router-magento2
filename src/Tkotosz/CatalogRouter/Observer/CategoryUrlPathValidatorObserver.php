<?php

namespace Tkotosz\CatalogRouter\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Tkotosz\CatalogRouter\Api\CatalogUrlPathProviderInterface;
use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Model\EntityData;
use Tkotosz\CatalogRouter\Model\Service\UrlPathUsedCheckerContainer;
use Tkotosz\CatalogRouter\Model\UrlPath;

class CategoryUrlPathValidatorObserver implements ObserverInterface
{
    /**
     * @var UrlPathUsedCheckerContainer
     */
    private $urlPathUsedChecker;
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @var CatalogUrlPathProviderInterface
     */
    private $urlPathProvider;
    
    /**
     * @param UrlPathUsedCheckerContainer     $urlPathUsedChecker
     * @param StoreManagerInterface           $storeManager
     * @param CatalogUrlPathProviderInterface $urlPathProvider
     */
    public function __construct(UrlPathUsedCheckerContainer $urlPathUsedChecker, StoreManagerInterface $storeManager, CatalogUrlPathProviderInterface $urlPathProvider)
    {
        $this->urlPathUsedChecker = $urlPathUsedChecker;
        $this->storeManager = $storeManager;
        $this->urlPathProvider = $urlPathProvider;
    }
    
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Category $category */
        $category = $observer->getEvent()->getCategory();

        foreach ($category->getStoreIds() as $storeId) {
            if (!$storeId) continue;
            $urlPath = $this->urlPathProvider->getCategoryUrlPath($category->getId(), $storeId);
            $resolvedEntities = $this->urlPathUsedChecker->check($urlPath, $storeId);

            if (count($resolvedEntities) > 1) {
                $store = $this->storeManager->getStore($storeId);
                $messages = [];
                foreach ($resolvedEntities as $entity) {
                    if ($entity->getType() == 'category' && $entity->getId() == $category->getId()) {
                        continue;
                    }
                    $messages[] = __(
                        'The "%1" url path already used by a %2 with id %3 in the %4 (storeid: %5) store',
                        $urlPath->getIdentifier(),
                        $entity->getType(),
                        $entity->getId(),
                        $store->getName(),
                        $store->getId()
                    );
                }

                throw new \Magento\Framework\Exception\AlreadyExistsException(__(join("<br>", $messages)));
            }
        }
    }
}
