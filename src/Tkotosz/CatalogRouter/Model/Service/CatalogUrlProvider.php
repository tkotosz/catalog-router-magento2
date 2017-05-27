<?php

namespace Tkotosz\CatalogRouter\Model\Service;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Tkotosz\CatalogRouter\Api\CatalogUrlPathProviderInterface;
use Tkotosz\CatalogRouter\Api\CatalogUrlProviderInterface;

class CatalogUrlProvider implements CatalogUrlProviderInterface
{   
    /**
     * @var CatalogUrlPathProviderInterface
     */
    private $catalogUrlPathProvider;
   
    /**
     * @var UrlInterface
     */
    private $urlProvider;
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @param CatalogUrlPathProviderInterface $catalogUrlPathProvider
     * @param UrlInterface                    $urlProvider
     * @param StoreManagerInterface           $storeManager
     */
    public function __construct(
        CatalogUrlPathProviderInterface $catalogUrlPathProvider,
        UrlInterface $urlProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->catalogUrlPathProvider = $catalogUrlPathProvider;
        $this->urlProvider = $urlProvider;
        $this->storeManager = $storeManager;
    }
    
    /**
     * @param int   $categoryId
     * @param int   $storeId
     * @param array $params
     *
     * @return string
     */
    public function getCategoryUrl(int $categoryId, int $storeId, array $params = []) : string
    {
        $urlPath = $this->catalogUrlPathProvider->getCategoryUrlPath($categoryId, $storeId);

        return $this->getUrl($urlPath->getFullPath(), $storeId, $params);
    }

    /**
     * @param int   $productId
     * @param int   $storeId
     * @param array $params
     *
     * @return string
     */
    public function getProductUrl(int $productId, int $storeId, array $params = []) : string
    {
        $urlPath = $this->catalogUrlPathProvider->getProductUrlPath($productId, $storeId);

        return $this->getUrl($urlPath->getFullPath(), $storeId, $params);
    }

    /**
     * @param string $urlPath
     * @param int    $storeId
     * @param array  $params
     *
     * @return string
     */
    private function getUrl(string $urlPath, int $storeId, array $params = []) : string
    {
        if ($storeId != $this->storeManager->getStore()->getId()) {
            $params['_scope_to_url'] = true;
        }

        return $this->urlProvider->setScope($storeId)->getDirectUrl($urlPath, $params);
    }
}
