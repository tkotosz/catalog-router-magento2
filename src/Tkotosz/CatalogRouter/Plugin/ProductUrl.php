<?php

namespace Tkotosz\CatalogRouter\Plugin;

use Tkotosz\CatalogRouter\Api\CatalogUrlProviderInterface;
use Tkotosz\CatalogRouter\Model\Exception\CatalogEntityNotFoundException;
use Tkotosz\CatalogRouter\Model\Service\StoreIdResolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Url as DefaultProductUrlModel;
use Magento\Store\Model\StoreManagerInterface;

class ProductUrl
{
    /**
     * @var CatalogUrlProviderInterface
     */
    private $catalogUrlProvider;
    
    /**
     * @var StoreIdResolver
     */
    private $storeIdResolver;
    
    /**
     * @param CatalogUrlProviderInterface $catalogUrlProvider
     * @param StoreIdResolver             $storeIdResolver
     */
    public function __construct(CatalogUrlProviderInterface $catalogUrlProvider, StoreIdResolver $storeIdResolver)
    {
        $this->catalogUrlProvider = $catalogUrlProvider;
        $this->storeIdResolver = $storeIdResolver;
    }
    
    /**
     * @param  DefaultProductUrlModel $urlModel
     * @param  \Closure               $proceed
     * @param  Product                $product
     * @param  array                  $params
     *
     * @return string
     */
    public function aroundGetUrl(DefaultProductUrlModel $urlModel, \Closure $proceed, Product $product, $params = [])
    {
        try {
            $storeId = $this->storeIdResolver->resolve($product, $params);
            $url = $this->catalogUrlProvider->getProductUrl($product->getId(), $storeId, $params);
        } catch (CatalogEntityNotFoundException $e) {
            $url = $proceed($product, $params);
        }

        return $url;
    }
}
