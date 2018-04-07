<?php

namespace Tkotosz\CatalogRouter\Model\Service\UrlPathUsedChecker;

use Magento\Store\Model\StoreManagerInterface;
use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Api\UrlPathUsedChecker;
use Tkotosz\CatalogRouter\Model\EntityData;
use Tkotosz\CatalogRouter\Model\Service\CatalogUrlPathResolver;
use Tkotosz\CatalogRouter\Model\UrlPath;

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
     * @var CatalogUrlPathResolver
     */
    private $catalogUrlPathResolver;
    
    /**
     * @param CategoryResolverInterface $categoryResolver
     * @param StoreManagerInterface     $storeManager
     * @param CatalogUrlPathResolver    $catalogUrlPathResolver
     */
    public function __construct(
        CategoryResolverInterface $categoryResolver,
        StoreManagerInterface $storeManager,
        CatalogUrlPathResolver $catalogUrlPathResolver
    ) {
        $this->categoryResolver = $categoryResolver;
        $this->storeManager = $storeManager;
        $this->catalogUrlPathResolver = $catalogUrlPathResolver;
    }
    
    /**
     * @param UrlPath $urlPath
     * @param int     $storeId
     *
     * @return EntityData[]
     */
    public function check(UrlPath $urlPath, int $storeId) : array
    {
        $parentCategoryId = $this->catalogUrlPathResolver->resolveParentCategoryId($urlPath, $storeId);

        return $this->categoryResolver->resolveAllByUrlKey($urlPath->getLastPart(), $storeId, $parentCategoryId);
    }
}
