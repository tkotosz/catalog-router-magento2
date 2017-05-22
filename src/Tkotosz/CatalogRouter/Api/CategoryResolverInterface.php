<?php

namespace Tkotosz\CatalogRouter\Api;

use Tkotosz\CatalogRouter\Model\CatalogEntity;

interface CategoryResolverInterface
{
    /**
     * @param string $urlKey
     * @param int $storeId
     * @param int $parentId
     *
     * @return CatalogEntity
     */
    public function resolveByUrlKey(string $urlKey, int $storeId, int $parentId) : CatalogEntity;

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return CatalogEntity
     */
    public function resolveById(int $categoryId, int $storeId) : CatalogEntity;

    /**
     * @param int $categoryId
     *
     * @return int[]
     */
    public function resolveParentIds(int $categoryId) : array;
}
