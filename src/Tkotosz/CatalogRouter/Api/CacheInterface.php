<?php

namespace Tkotosz\CatalogRouter\Api;

interface CacheInterface
{
    public function set(string $key, $value);

    public function has(string $key);

    public function get(string $key);
}
