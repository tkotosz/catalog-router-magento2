<?php

namespace Tkotosz\CatalogRouter\Model;

class CatalogEntity
{
    /**
     * @var string
     */
    private $type;
    
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var string
     */
    private $urlKey;
    
    /**
     * @param string $type
     * @param int    $id
     * @param string $urlKey
     */
    public function __construct(string $type, int $id, string $urlKey)
    {
        $this->type = $type;
        $this->id = $id;
        $this->urlKey = $urlKey;
    }
    
    public function getType() : string
    {
        return $this->type;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getUrlKey() : string
    {
        return $this->urlKey;
    }
}
