<?php

namespace Tkotosz\CatalogRouter\Model\Service;

use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Model\EntityData;
use Tkotosz\CatalogRouter\Model\Exception\EntityDataNotFoundException;
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
     * @return EntityData
     */
    public function resolve(UrlPath $urlPath, int $storeId) : EntityData
    {
        $parentCategoryId = $this->resolveParentCategoryId($urlPath, $storeId);
        
        try {
            $entity = $this->resolveCategory($urlPath->getLastPart(), $storeId, $parentCategoryId);
        } catch (EntityDataNotFoundException $e) {
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
    public function resolveParentCategoryId(UrlPath $urlPath, int $storeId) : int
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
     * @return EntityData
     */
    private function resolveCategory(string $urlKey, int $storeId, int $parentCategoryId) : EntityData
    {
        return $this->categoryResolver->resolveByUrlKey($urlKey, $storeId, $parentCategoryId);
    }

    /**
     * @param string $urlKey
     * @param int    $storeId
     * @param int    $categoryId
     *
     * @return EntityData
     */
    private function resolveProductInCategory(string $urlKey, int $storeId, int $categoryId) : EntityData
    {
        $product = $this->productResolver->resolveByUrlKey($urlKey, $storeId);

        $productCategoryIds = $this->productResolver->resolveCategoryIds($product->getId(), $storeId);
                
        if (!in_array($categoryId, $productCategoryIds)) {
            throw new EntityDataNotFoundException('product is not found in the given category');
        }

        return $product;
    }
}
