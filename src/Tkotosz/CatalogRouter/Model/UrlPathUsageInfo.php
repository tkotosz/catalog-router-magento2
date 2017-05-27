<?php

namespace Tkotosz\CatalogRouter\Model;

class UrlPathUsageInfo
{
    /**
     * @var int
     */
    private $entityId;

    /**
     * @var string
     */
    private $entityType;
    
    /**
     * @param int    $entityId
     * @param string $entityType
     */
    public function __construct(int $entityId, string $entityType)
    {
        $this->entityId = $entityId;
        $this->entityType = $entityType;
    }
    
    public function getEntityId() : int
    {
        return $this->entityId;
    }

    public function getEntityType() : string
    {
        return $this->entityType;
    }
}
