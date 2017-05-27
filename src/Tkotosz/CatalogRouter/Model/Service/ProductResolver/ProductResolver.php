<?php

namespace Tkotosz\CatalogRouter\Model\Service\ProductResolver;

use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Model\EntityData;
use Tkotosz\CatalogRouter\Model\Exception\EntityDataNotFoundException;
use Magento\Catalog\Model\ResourceModel\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ProductResolver implements ProductResolverInterface
{
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;
    
    /**
     * @var ProductFactory
     */
    private $productFactory;
    
    /**
     * @param ProductCollectionFactory  $productCollectionFactory
     * @param ProductFactory            $productFactory
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        ProductFactory $productFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
    }
    
    /**
     * @param string $urlKey
     * @param int    $storeId
     *
     * @return EntityData
     */
    public function resolveByUrlKey(string $urlKey, int $storeId) : EntityData
    {
        $productId = $this->productCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('url_key', $urlKey)
            ->getFirstItem()
            ->getId();

        if (!$productId) {
            throw new EntityDataNotFoundException('Product does not exist');
        }

        return new EntityData('product', $productId, $urlKey);
    }

    /**
     * @param string $urlKey
     * @param int    $storeId
     *
     * @return EntityData[]
     */
    public function resolveAllByUrlKey(string $urlKey, int $storeId) : array
    {
        $products = [];

        $productCollection = $this->productCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('url_key', $urlKey);

        foreach ($productCollection as $product) {
            $products[] = new EntityData('product', $product->getId(), $urlKey);        
        }

        return $products;
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return EntityData
     */
    public function resolveById(int $productId, int $storeId) : EntityData
    {
        $urlKey = $this->productCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addAttributeToSelect('url_key')
            ->addAttributeToFilter('entity_id', $productId)
            ->getFirstItem()
            ->getUrlKey();

        if (!$urlKey) {
            throw new EntityDataNotFoundException('Product does not exist');
        }

        return new EntityData('product', $productId, $urlKey);
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return int[]
     */
    public function resolveCategoryIds(int $productId, int $storeId) : array
    {
        $productResource = $this->productFactory->create();
        $connection = $productResource->getConnection();
        
        $select = $connection->select()
            ->distinct()
            ->from($productResource->getTable('catalog_category_product_index'), ['category_id'])
            ->where('store_id = ?', $storeId)
            ->where('product_id = ?', $productId);

        return $connection->fetchCol($select);
    }
}
