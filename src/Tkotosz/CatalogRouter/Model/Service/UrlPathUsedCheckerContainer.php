<?php

namespace Tkotosz\CatalogRouter\Model\Service;

use Tkotosz\CatalogRouter\Api\UrlPathUsedChecker;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Model\UrlPathUsageInfo;

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

    public function check(UrlPath $urlPath, int $storeId) : array
    {
        $result = [];

        foreach ($this->checkers as $checker) {
            $result = array_merge($result, $checker->check($urlPath, $storeId));
        }

        return $result;;
    }
}
