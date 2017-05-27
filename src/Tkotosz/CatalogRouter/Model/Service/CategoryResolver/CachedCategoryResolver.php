<?php

namespace Tkotosz\CatalogRouter\Model\Service\CategoryResolver;

use Tkotosz\CatalogRouter\Api\CacheInterface;
use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Model\EntityData;

class CachedCategoryResolver implements CategoryResolverInterface
{
    const CACHE_KEY_RESOLVE_BY_URL_KEY = 'resolve_category_by_ulkey';
    const CACHE_KEY_RESOLVE_BY_ID = 'resolve_category_by_id';
    const CACHE_KEY_RESOLVE_PARENT_IDS = 'resolve_category_parent_ids';

    /**
     * @var CategoryResolverInterface
     */
    private $categoryResolver;
    
    /**
     * @var CacheInterface
     */
    private $cache;
    
    /**
     * @param CategoryResolverInterface $categoryResolver
     * @param CacheInterface            $cache
     */
    public function __construct(CategoryResolverInterface $categoryResolver, CacheInterface $cache)
    {
        $this->categoryResolver = $categoryResolver;
        $this->cache = $cache;
    }
    
    /**
     * @param string $urlKey
     * @param int    $storeId
     * @param int    $parentId
     *
     * @return EntityData
     */
    public function resolveByUrlKey(string $urlKey, int $storeId, int $parentId) : EntityData
    {
        $cacheKey = implode('_', [self::CACHE_KEY_RESOLVE_BY_URL_KEY, $urlKey, $storeId, $parentId]);

        if (!$this->cache->has($cacheKey)) {
            $data = $this->categoryResolver->resolveByUrlKey($urlKey, $storeId, $parentId);
            $this->cache->set($cacheKey, $data);
        }
        
        return $this->cache->get($cacheKey);
    }

    public function resolveAllByUrlKey(string $urlKey, int $storeId, int $parentId) : array
    {
        return $this->categoryResolver->resolveAllByUrlKey($urlKey, $storeId, $parentId);
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return EntityData
     */
    public function resolveById(int $categoryId, int $storeId) : EntityData
    {
        $cacheKey = implode('_', [self::CACHE_KEY_RESOLVE_BY_ID, $storeId, $categoryId]);

        if (!$this->cache->has($cacheKey)) {
            $data = $this->categoryResolver->resolveById($categoryId, $storeId);
            $this->cache->set($cacheKey, $data);
        }
        
        return $this->cache->get($cacheKey);
    }

    /**
     * @param int $categoryId
     *
     * @return int[]
     */
    public function resolveParentIds(int $categoryId) : array
    {
        $cacheKey = implode('_', [self::CACHE_KEY_RESOLVE_PARENT_IDS, $categoryId]);

        if (!$this->cache->has($cacheKey)) {
            $data = $this->categoryResolver->resolveParentIds($categoryId);
            $this->cache->set($cacheKey, $data);
        }
        
        return $this->cache->get($cacheKey);
    }
}
