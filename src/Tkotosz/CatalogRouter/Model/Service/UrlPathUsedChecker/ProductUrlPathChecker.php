<?php

namespace Tkotosz\CatalogRouter\Model\Service\UrlPathUsedChecker;

use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Api\UrlPathUsedChecker;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Model\EntityData;

class ProductUrlPathChecker implements UrlPathUsedChecker
{
    /**
     * @var ProductResolverInterface
     */
    private $productResolver;
    
    /**
     * @param ProductResolverInterface $productResolver
     */
    public function __construct(ProductResolverInterface $productResolver)
    {
        $this->productResolver = $productResolver;
    }
    
    /**
     * @param UrlPath $urlPath
     * @param int     $storeId
     *
     * @return EntityData[]
     */
    public function check(UrlPath $urlPath, int $storeId) : array
    {
        return $this->productResolver->resolveAllByUrlKey($urlPath->getLastPart(), $storeId);
    }
}
