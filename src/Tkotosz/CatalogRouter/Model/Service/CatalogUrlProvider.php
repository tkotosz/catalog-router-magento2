<?php

namespace Tkotosz\CatalogRouter\Model\Service;

use Tkotosz\CatalogRouter\Api\CatalogUrlProviderInterface;
use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;

class CatalogUrlProvider implements CatalogUrlProviderInterface
{
    /**
     * @var CategoryResolverInterface
     */
    private $categoryResolver;
    
    /**
     * @var ProductResolverInterface
     */
    private $productResolver;
    
    /**
     * @var Url
     */
    private $urlProvider;
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @param CategoryResolverInterface $categoryResolver
     * @param ProductResolverInterface  $productResolver
     * @param Url              $urlProvider
     * @param StoreManagerInterface     $storeManager
     */
    public function __construct(
        CategoryResolverInterface $categoryResolver,
        ProductResolverInterface $productResolver,
        Url $urlProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryResolver = $categoryResolver;
        $this->productResolver = $productResolver;
        $this->urlProvider = $urlProvider;
        $this->storeManager = $storeManager;
    }
    
    /**
     * @param int   $categoryId
     * @param int   $storeId
     * @param array $params
     *
     * @return string
     */
    public function getCategoryUrl(int $categoryId, int $storeId, array $params = []) : string
    {
        $urlPath = '';

        foreach ($this->categoryResolver->resolveParentIds($categoryId) as $parentCategoryId) {
            $urlPath .= '/' . $this->categoryResolver->resolveById($parentCategoryId, $storeId)->getUrlKey();
        }

        $urlPath .= '/' . $this->categoryResolver->resolveById($categoryId, $storeId)->getUrlKey();

        return $this->getUrl($urlPath, $storeId, $params);
    }

    /**
     * @param int   $productId
     * @param int   $storeId
     * @param array $params
     *
     * @return string
     */
    public function getProductUrl(int $productId, int $storeId, array $params = []) : string
    {
        $product = $this->productResolver->resolveById($productId, $storeId);

        return $this->getUrl($product->getUrlKey(), $storeId, $params);
    }

    /**
     * @param string $urlPath
     * @param int    $storeId
     * @param array  $params
     *
     * @return string
     */
    private function getUrl(string $urlPath, int $storeId, array $params = []) : string
    {
        if ($storeId != $this->storeManager->getStore()->getId()) {
            $params['_scope_to_url'] = true;
        }

        return $this->urlProvider->setScope($storeId)->getDirectUrl($urlPath, $params);
    }
}
