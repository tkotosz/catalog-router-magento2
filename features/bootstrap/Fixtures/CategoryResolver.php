<?php

namespace Fixtures;

use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Model\CatalogEntity;
use Tkotosz\CatalogRouter\Model\Exception\CatalogEntityNotFoundException;

class CategoryResolver implements CategoryResolverInterface
{
    /**
     * @var array
     */
    private $categories;
    
    /**
     * @param array        $categories
     */
    public function __construct(array $categories)
    {
        $this->categories = $categories;
    }
    
    /**
     * @param string $urlKey
     * @param int $storeId
     * @param int $parentId
     *
     * @return CatalogEntity
     */
    public function resolveByUrlKey(string $urlKey, int $storeId, int $parentId) : CatalogEntity
    {
        foreach ($this->categories as $category) {
            $urlKeys = $category->getData('url_key');
            $categoryUrlKeyInStore = isset($urlKeys[$storeId]) ? $urlKeys[$storeId] : $urlKeys[0];

            if ($categoryUrlKeyInStore == $urlKey && $category->getData('parent_id') == $parentId) {
                return new CatalogEntity('category', $category->getId(), $urlKey);
            }
        }

        throw new CatalogEntityNotFoundException('not found!');
        
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return CatalogEntity
     */
    public function resolveById(int $categoryId, int $storeId) : CatalogEntity
    {
        throw new \Exception(__METHOD__ . 'Method not implemented');
    }

    /**
     * @param int $categoryId
     *
     * @return int[]
     */
    public function resolveParentIds(int $categoryId) : array
    {
        throw new \Exception(__METHOD__ . 'Method not implemented');
    }
}
