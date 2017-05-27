<?php

namespace Tkotosz\CatalogRouter\Api;

use Tkotosz\CatalogRouter\Model\EntityData;

interface ProductResolverInterface
{
    /**
     * @param  string   $urlKey
     *
     * @return EntityData
     */
    public function resolveByUrlKey(string $urlKey, int $storeId) : EntityData;

    /**
     * @param  int    $productId
     *
     * @return EntityData
     */
    public function resolveById(int $productId, int $storeId) : EntityData;

    /**
     * @param  int    $productId
     *
     * @return int[]
     */
    public function resolveCategoryIds(int $productId, int $storeId) : array;
}
