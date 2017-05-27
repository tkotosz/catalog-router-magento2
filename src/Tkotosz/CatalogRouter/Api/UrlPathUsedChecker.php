<?php

namespace Tkotosz\CatalogRouter\Api;

use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Model\UrlPathUsageInfo;

interface UrlPathUsedChecker
{
    /**
     * @param UrlPath $urlPath
     * @param int     $storeId
     *
     * @return UrlPathUsageInfo[]
     */
    public function check(UrlPath $urlPath, int $storeId) : array;
}
