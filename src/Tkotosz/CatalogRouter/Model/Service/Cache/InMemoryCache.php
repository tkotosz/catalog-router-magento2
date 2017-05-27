<?php

namespace Tkotosz\CatalogRouter\Model\Service\Cache;

use Tkotosz\CatalogRouter\Api\CacheInterface;

class InMemoryCache implements CacheInterface
{
    /**
     * @var array
     */
    private $cache;

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value)
    {
        $this->cache[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return boolean
     */
    public function has(string $key) : bool
    {
        return isset($this->cache[$key]);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        if (!isset($this->cache[$key])) {
            throw new \Exception(sprintf('%s cache does not contain %s key', $storage, $key));
        }
        
        return $this->cache[$key];
    }
}
