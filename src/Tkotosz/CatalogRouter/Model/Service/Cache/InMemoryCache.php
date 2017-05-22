<?php

namespace Tkotosz\CatalogRouter\Model\Service\Cache;

use Tkotosz\CatalogRouter\Api\CacheInterface;

class InMemoryCache implements CacheInterface
{
    /**
     * @var array
     */
    private $cache;

    public function set(string $key, $value)
    {
        $this->cache[$key] = $value;
    }

    public function has(string $key)
    {
        return isset($this->cache[$key]);
    }

    public function get(string $key)
    {
        if (!isset($this->cache[$key])) {
            throw new \Exception(sprintf('%s cache does not contain %s key', $storage, $key));
        }
        
        return $this->cache[$key];
    }
}
