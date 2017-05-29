<?php

namespace Tkotosz\CatalogRouter\Api;

use Tkotosz\CatalogRouter\Model\EntityData;

interface CategoryResolverInterface
{
    /**
     * @param string $urlKey
     * @param int $storeId
     * @param int $parentId
     *
     * @return EntityData
     */
    public function resolveByUrlKey(string $urlKey, int $storeId, int $parentId) : EntityData;

    /**
     * @param string $urlKey
     * @param int    $storeId
     * @param int    $parentId
     *
     * @return EntityData[]
     */
    public function resolveAllByUrlKey(string $urlKey, int $storeId, int $parentId) : array;

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return EntityData
     */
    public function resolveById(int $categoryId, int $storeId) : EntityData;

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return int[]
     */
    public function resolveParentIds(int $categoryId, int $storeId) : array;
}
