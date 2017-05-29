<?php

namespace Fixtures;

use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Model\EntityData;
use Tkotosz\CatalogRouter\Model\Exception\EntityDataNotFoundException;

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
     * @return EntityData
     */
    public function resolveByUrlKey(string $urlKey, int $storeId, int $parentId) : EntityData
    {
        foreach ($this->categories as $category) {
            $urlKeys = $category->getData('url_key');
            $categoryUrlKeyInStore = isset($urlKeys[$storeId]) ? $urlKeys[$storeId] : $urlKeys[0];

            if ($categoryUrlKeyInStore == $urlKey && $category->getData('parent_id') == $parentId) {
                return new EntityData('category', $category->getId(), $urlKey);
            }
        }

        throw new EntityDataNotFoundException('not found!');
        
    }

    /**
     * @param string $urlKey
     * @param int    $storeId
     * @param int    $parentId
     *
     * @return EntityData[]
     */
    public function resolveAllByUrlKey(string $urlKey, int $storeId, int $parentId) : array
    {
        throw new \Exception(__METHOD__ . 'Method not implemented');
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return EntityData
     */
    public function resolveById(int $categoryId, int $storeId) : EntityData
    {
        throw new \Exception(__METHOD__ . 'Method not implemented');
    }

    /**
     * @param int $categoryId
     *
     * @return int[]
     */
    public function resolveParentIds(int $categoryId, int $storeId) : array
    {
        throw new \Exception(__METHOD__ . 'Method not implemented');
    }
}
