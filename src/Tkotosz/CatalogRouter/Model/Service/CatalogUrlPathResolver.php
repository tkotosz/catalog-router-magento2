<?php

namespace Tkotosz\CatalogRouter\Model\Service;

use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Model\CatalogEntity;
use Tkotosz\CatalogRouter\Model\Exception\CatalogEntityNotFoundException;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Magento\Store\Model\StoreManagerInterface;

class CatalogUrlPathResolver
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
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @param CategoryResolverInterface $categoryResolver
     * @param ProductResolverInterface  $productResolver
     * @param StoreManagerInterface     $storeManager
     */
    public function __construct(
        CategoryResolverInterface $categoryResolver,
        ProductResolverInterface $productResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryResolver = $categoryResolver;
        $this->productResolver = $productResolver;
        $this->storeManager = $storeManager;
    }
    
    /**
     * @param UrlPath $urlPath
     * @param int     $storeId
     *
     * @return CatalogEntity
     */
    public function resolve(UrlPath $urlPath, int $storeId) : CatalogEntity
    {
        $parentCategoryId = $this->resolveParentCategoryId($urlPath, $storeId);
        
        try {
            $entity = $this->resolveCategory($urlPath->getLastPart(), $storeId, $parentCategoryId);
        } catch (CatalogEntityNotFoundException $e) {
            $entity = $this->resolveProductInCategory($urlPath->getLastPart(), $storeId, $parentCategoryId);
        }

        return $entity;
    }

    /**
     * @param UrlPath $urlPath
     * @param int     $storeId
     *
     * @return int
     */
    private function resolveParentCategoryId(UrlPath $urlPath, int $storeId) : int
    {
        $parentCategoryId = $this->storeManager->getStore($storeId)->getRootCategoryId();

        foreach ($urlPath->getBeginningParts() as $urlKey) {
            $parentCategoryId = $this->resolveCategory($urlKey, $storeId, $parentCategoryId)->getId();
        }

        return $parentCategoryId;
    }

    /**
     * @param string $urlKey
     * @param int    $storeId
     * @param int    $parentCategoryId
     *
     * @return CatalogEntity
     */
    private function resolveCategory(string $urlKey, int $storeId, int $parentCategoryId) : CatalogEntity
    {
        return $this->categoryResolver->resolveByUrlKey($urlKey, $storeId, $parentCategoryId);
    }

    /**
     * @param string $urlKey
     * @param int    $storeId
     * @param int    $categoryId
     *
     * @return CatalogEntity
     */
    private function resolveProductInCategory(string $urlKey, int $storeId, int $categoryId) : CatalogEntity
    {
        $product = $this->productResolver->resolveByUrlKey($urlKey, $storeId);

        $productCategoryIds = $this->productResolver->resolveCategoryIds($product->getId(), $storeId);
                
        if (!in_array($categoryId, $productCategoryIds)) {
            throw new CatalogEntityNotFoundException('product is not found in the given category');
        }

        return $product;
    }
}
