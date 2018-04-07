<?php

namespace Tkotosz\CatalogRouter\Model\Service\ProductResolver;

use Tkotosz\CatalogRouter\Api\CacheInterface;
use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Model\EntityData;
use Tkotosz\CatalogRouter\Model\Exception\EntityDataNotFoundException;
use Magento\Catalog\Model\ResourceModel\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CachedProductResolver implements ProductResolverInterface
{
    const CACHE_KEY_RESOLVE_BY_URL_KEY = 'resolve_product_by_ulkey';
    const CACHE_KEY_RESOLVE_BY_ID = 'resolve_product_by_id';
    const CACHE_KEY_RESOLVE_CATEGORIES = 'resolve_product_categories';

    /**
     * @var ProductResolverInterface
     */
    private $productResolver;

    /**
     * @var CacheInterface
     */
    private $cache;
    
    /**
     * @param ProductResolverInterface $productResolver
     * @param CacheInterface $cache
     */
    public function __construct(ProductResolverInterface $productResolver, CacheInterface $cache)
    {
        $this->productResolver = $productResolver;
        $this->cache = $cache;
    }

    /**
     * @param string $urlKey
     * @param int    $storeId
     *
     * @return EntityData
     */
    public function resolveByUrlKey(string $urlKey, int $storeId) : EntityData
    {
        $cacheKey = implode('_', [self::CACHE_KEY_RESOLVE_BY_URL_KEY, $urlKey, $storeId]);

        if (!$this->cache->has($cacheKey)) {
            $data = $this->productResolver->resolveByUrlKey($urlKey, $storeId);
            $this->cache->set($cacheKey, $data);
        }
        
        return $this->cache->get($cacheKey);
    }

    /**
     * @param string $urlKey
     * @param int    $storeId
     *
     * @return EntityData[]
     */
    public function resolveAllByUrlKey(string $urlKey, int $storeId) : array
    {
        return $this->productResolver->resolveAllByUrlKey($urlKey, $storeId);
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return EntityData
     */
    public function resolveById(int $productId, int $storeId) : EntityData
    {
        $cacheKey = implode('_', [self::CACHE_KEY_RESOLVE_BY_ID, $productId, $storeId]);

        if (!$this->cache->has($cacheKey)) {
            $data = $this->productResolver->resolveById($productId, $storeId);
            $this->cache->set($cacheKey, $data);
        }
        
        return $this->cache->get($cacheKey);
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return int[]
     */
    public function resolveCategoryIds(int $productId, int $storeId) : array
    {
        $cacheKey = implode('_', [self::CACHE_KEY_RESOLVE_CATEGORIES, $productId, $storeId]);

        if (!$this->cache->has($cacheKey)) {
            $data = $this->productResolver->resolveCategoryIds($productId, $storeId);
            $this->cache->set($cacheKey, $data);
        }
        
        return $this->cache->get($cacheKey);
    }
}
