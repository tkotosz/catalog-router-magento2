<?php

namespace Tkotosz\CatalogRouter\Model\Service\UrlPathUsedChecker;

use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Api\UrlPathUsedChecker;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Model\UrlPathUsageInfo;

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
     * @return UrlPathUsageInfo[]
     */
    public function check(UrlPath $urlPath, int $storeId) : array
    {
        $result = [];

        foreach ($this->productResolver->resolveAllByUrlKey($urlPath->getLastPart(), $storeId) as $product) {
            $result[] = new UrlPathUsageInfo($product->getId(), 'product');
        }

        return $result;
    }
}
