<?php

namespace Tkotosz\CatalogRouter\Api;

use Tkotosz\CatalogRouter\Model\UrlPath;

interface CatalogUrlPathProviderInterface
{
    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return UrlPath
     */
    public function getCategoryUrlPath(int $categoryId, int $storeId) : UrlPath;

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return UrlPath
     */
    public function getProductUrlPath(int $productId, int $storeId) : UrlPath;
}
