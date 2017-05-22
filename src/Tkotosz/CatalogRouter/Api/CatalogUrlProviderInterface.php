<?php

namespace Tkotosz\CatalogRouter\Api;

interface CatalogUrlProviderInterface
{
    /**
     * @param int   $categoryId
     * @param int   $storeId
     * @param array $params
     *
     * @return string
     */
    public function getCategoryUrl(int $categoryId, int $storeId, array $params = []) : string;

    /**
     * @param int   $productId
     * @param int   $storeId
     * @param array $params
     *
     * @return string
     */
    public function getProductUrl(int $productId, int $storeId, array $params = []) : string;
}
