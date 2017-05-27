<?php

namespace Tkotosz\CatalogRouter\Api;

use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Model\EntityData;

interface UrlPathUsedChecker
{
    /**
     * @param UrlPath $urlPath
     * @param int     $storeId
     *
     * @return EntityData[]
     */
    public function check(UrlPath $urlPath, int $storeId) : array;
}
