<?php

namespace Tkotosz\CatalogRouter\Model\Service\UrlPathUsedChecker;

use Magento\Store\Model\StoreManagerInterface;
use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Api\UrlPathUsedChecker;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Model\UrlPathUsageInfo;

class CategoryUrlPathChecker implements UrlPathUsedChecker
{
    /**
     * @var CategoryResolverInterface
     */
    private $categoryResolver;
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @param CategoryResolverInterface $categoryResolver
     * @param StoreManagerInterface     $storeManager
     */
    public function __construct(CategoryResolverInterface $categoryResolver, StoreManagerInterface $storeManager)
    {
        $this->categoryResolver = $categoryResolver;
        $this->storeManager = $storeManager;
    }
    
    /**
     * @param UrlPath $urlPath
     * @param int     $storeId
     *
     * @return UrlPathUsageInfo[]
     */
    public function check(UrlPath $urlPath, int $storeId) : array
    {
        $result = [];

        $parentCategoryId = $this->resolveParentCategoryId($urlPath, $storeId);

        foreach ($this->categoryResolver->resolveAllByUrlKey($urlPath->getLastPart(), $storeId, $parentCategoryId) as $category) {
            $result[] = new UrlPathUsageInfo($category->getId(), 'category');
        }

        return $result;
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
            $parentCategoryId = $this->categoryResolver->resolveByUrlKey($urlKey, $storeId, $parentCategoryId)->getId();
        }

        return $parentCategoryId;
    }
}
