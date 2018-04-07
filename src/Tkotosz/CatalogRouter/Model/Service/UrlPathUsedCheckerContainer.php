<?php

namespace Tkotosz\CatalogRouter\Model\Service;

use Tkotosz\CatalogRouter\Api\UrlPathUsedChecker;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Model\EntityData;

class UrlPathUsedCheckerContainer
{
    /**
     * @var UrlPathUsedChecker[]
     */
    private $checkers;
    
    /**
     * @param UrlPathUsedChecker[] $checkers
     */
    public function __construct(array $checkers)
    {
        $this->checkers = $checkers;
    }

    /**
     * @param UrlPath $urlPath
     * @param int     $storeId
     *
     * @return EntityData[]
     */
    public function check(UrlPath $urlPath, int $storeId) : array
    {
        $result = [];

        foreach ($this->checkers as $checker) {
            $result = array_merge($result, $checker->check($urlPath, $storeId));
        }

        return $result;;
    }
}
