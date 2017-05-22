<?php

namespace Tkotosz\CatalogRouter\Model\Service\CategoryResolver;

use Tkotosz\CatalogRouter\Api\CategoryResolverInterface;
use Tkotosz\CatalogRouter\Model\CatalogEntity;
use Tkotosz\CatalogRouter\Model\Exception\CatalogEntityNotFoundException;
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
     * @return CatalogEntity
     */
    public function resolveByUrlKey(string $urlKey, int $storeId, int $parentId) : CatalogEntity
    {
        $categoryId = $this->categoryCollectionFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToFilter('url_key', $urlKey)
            ->addFieldToFilter('parent_id', $parentId)
            ->getFirstItem()
            ->getId();

        if (!$categoryId) {
            throw new CatalogEntityNotFoundException('Category does not exist');
        }

        return new CatalogEntity('category', $categoryId, $urlKey);
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return CatalogEntity
     */
    public function resolveById(int $categoryId, int $storeId) : CatalogEntity
    {
        $urlKey = $this->categoryCollectionFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToSelect('url_key')
            ->addFieldToFilter('entity_id', $categoryId)
            ->getFirstItem()
            ->getUrlKey();

        if (!$urlKey) {
            throw new CatalogEntityNotFoundException('Category does not exist');
        }

        return new CatalogEntity('category', $categoryId, $urlKey);
    }

    /**
     * @param int $categoryId
     *
     * @return int[]
     */
    public function resolveParentIds(int $categoryId) : array
    {
        $idPath = $this->categoryCollectionFactory->create()
            ->addFieldToSelect('path')
            ->addFieldToFilter('entity_id', $categoryId)
            ->getFirstItem()
            ->getPath();

        if (!$idPath) {
            throw new CatalogEntityNotFoundException('Category does not exist');
        }

        $parents = explode('/', $idPath);

        $toIgnore = [
            Category::TREE_ROOT_ID,
            $this->storeManager->getStore()->getRootCategoryId(),
            $categoryId
        ];
        
        return array_diff($parents, $toIgnore);
    }
}
