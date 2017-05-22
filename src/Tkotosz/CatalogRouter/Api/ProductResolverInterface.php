<?php

namespace Tkotosz\CatalogRouter\Api;

use Tkotosz\CatalogRouter\Model\CatalogEntity;

interface ProductResolverInterface
{
    /**
     * @param  string   $urlKey
     *
     * @return CatalogEntity
     */
    public function resolveByUrlKey(string $urlKey, int $storeId) : CatalogEntity;

    /**
     * @param  int    $productId
     *
     * @return CatalogEntity
     */
    public function resolveById(int $productId, int $storeId) : CatalogEntity;

    /**
     * @param  int    $productId
     *
     * @return int[]
     */
    public function resolveCategoryIds(int $productId, int $storeId) : array;
}
