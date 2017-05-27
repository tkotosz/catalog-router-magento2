<?php

namespace Tkotosz\CatalogRouter\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Model\Service\UrlPathUsedCheckerContainer;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Model\EntityData;

class ProductUrlPathValidatorObserver implements ObserverInterface
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
     * @var ProductResolverInterface
     */
    private $productResolver;
    
    /**
     * @param UrlPathUsedCheckerContainer $urlPathUsedChecker
     * @param StoreManagerInterface       $storeManager
     * @param ProductResolverInterface    $productResolver
     */
    public function __construct(UrlPathUsedCheckerContainer $urlPathUsedChecker, StoreManagerInterface $storeManager, ProductResolverInterface $productResolver)
    {
        $this->urlPathUsedChecker = $urlPathUsedChecker;
        $this->storeManager = $storeManager;
        $this->productResolver = $productResolver;
    }
    
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
         /** @var Product $product */
        $product = $observer->getEvent()->getProduct();

        foreach ($product->getStoreIds() as $storeId) {
            $urlKeyInStore = $this->productResolver->resolveById($product->getId(), $storeId)->getUrlKey();
            $urlPath = new UrlPath($urlKeyInStore);
            $resolvedEntities = $this->urlPathUsedChecker->check($urlPath, $storeId);

            if (count($resolvedEntities) > 1) {
                $store = $this->storeManager->getStore($storeId);
                $messages = [];
                foreach ($resolvedEntities as $entity) {
                    if ($entity->getType() == 'product' && $entity->getId() == $product->getId()) {
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

                throw new \Exception(join("<br>", $messages));
            }
        }
    }
}
