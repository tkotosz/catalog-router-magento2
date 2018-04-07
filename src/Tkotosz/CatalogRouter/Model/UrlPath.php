<?php

namespace Tkotosz\CatalogRouter\Model;

class UrlPath
{
    /**
     * @var string[]
     */
    private $urlKeys;

    /**
     * @var string
     */
    private $identifier;
    
    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->identifier = trim($path, '/');
        $this->urlKeys = explode('/', $this->identifier);
    }

    /**
     * @return string
     */
    public function getIdentifier() : string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getFullPath() : string
    {
        return $this->getIdentifier();
    }
    
    /**
     * @return string[]
     */
    public function getAllPart() : array
    {
        return $this->urlKeys;
    }

    /**
     * @return array
     */
    public function getBeginningParts() : array
    {
        return array_slice($this->urlKeys, 0, -1);
    }

    /**
     * @return string
     */
    public function getLastPart() : string
    {
        return end($this->urlKeys);
    }
}
