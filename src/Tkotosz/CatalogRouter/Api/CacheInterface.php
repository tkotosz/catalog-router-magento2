<?php

namespace Tkotosz\CatalogRouter\Api;

interface CacheInterface
{
    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value);

    /**
     * @param string $key
     *
     * @return boolean
     */
    public function has(string $key) : bool;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key);
}
