<?php

namespace Tkotosz\CatalogRouter\Model\Service;

use Tkotosz\CatalogRouter\Api\CatalogUrlPathProviderInterface;
use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Model\UrlPath;

class CatalogUrlPathProvider implements CatalogUrlPathProviderInterface
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
     * @param CategoryResolverInterface $categoryResolver
     * @param ProductResolverInterface  $productResolver
     */
    public function __construct(CategoryResolverInterface $categoryResolver, ProductResolverInterface $productResolver)
    {
        $this->categoryResolver = $categoryResolver;
        $this->productResolver = $productResolver;
    }
    
    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return UrlPath
     */
    public function getCategoryUrlPath(int $categoryId, int $storeId) : UrlPath
    {
        $urlPath = '';

        foreach ($this->categoryResolver->resolveParentIds($categoryId, $storeId) as $parentCategoryId) {
            $urlPath .= '/' . $this->categoryResolver->resolveById($parentCategoryId, $storeId)->getUrlKey();
        }

        $urlPath .= '/' . $this->categoryResolver->resolveById($categoryId, $storeId)->getUrlKey();

        return new UrlPath($urlPath);
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return UrlPath
     */
    public function getProductUrlPath(int $productId, int $storeId) : UrlPath
    {
        $urlPath = $this->productResolver->resolveById($productId, $storeId)->getUrlKey();

        return new UrlPath($urlPath);
    }
}
