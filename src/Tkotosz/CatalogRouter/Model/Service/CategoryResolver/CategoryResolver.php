<?php

namespace Tkotosz\CatalogRouter\Model\Service\CategoryResolver;

use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Model\EntityData;
use Tkotosz\CatalogRouter\Model\Exception\EntityDataNotFoundException;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CategoryResolver implements CategoryResolverInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface     $storeManager
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
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
        $categoryId = $this->categoryCollectionFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToFilter('url_key', $urlKey)
            ->addFieldToFilter('parent_id', $parentId)
            ->getFirstItem()
            ->getId();

        if (!$categoryId) {
            throw new EntityDataNotFoundException('Category does not exist');
        }

        return new EntityData('category', $categoryId, $urlKey);
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
        $categories = [];
        
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToFilter('url_key', $urlKey)
            ->addFieldToFilter('parent_id', $parentId);

        foreach ($categoryCollection as $category) {
            $categories[] = new EntityData('category', $category->getId(), $urlKey);        
        }

        return $categories;
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return EntityData
     */
    public function resolveById(int $categoryId, int $storeId) : EntityData
    {
        $urlKey = $this->categoryCollectionFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToSelect('url_key')
            ->addFieldToFilter('entity_id', $categoryId)
            ->getFirstItem()
            ->getUrlKey();

        if (!$urlKey) {
            throw new EntityDataNotFoundException('Category does not exist');
        }

        return new EntityData('category', $categoryId, $urlKey);
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return int[]
     */
    public function resolveParentIds(int $categoryId, int $storeId) : array
    {
        $idPath = $this->categoryCollectionFactory->create()
            ->addFieldToSelect('path')
            ->addFieldToFilter('entity_id', $categoryId)
            ->getFirstItem()
            ->getPath();

        if (!$idPath) {
            throw new EntityDataNotFoundException('Category does not exist');
        }

        $parents = explode('/', $idPath);

        $toIgnore = [
            Category::TREE_ROOT_ID,
            Category::ROOT_CATEGORY_ID,
            $this->storeManager->getStore($storeId)->getRootCategoryId(),
            $categoryId
        ];
        
        return array_diff($parents, $toIgnore);
    }
}
