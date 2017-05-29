<?php

namespace Fixtures;

use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Model\EntityData;
use Tkotosz\CatalogRouter\Model\Exception\EntityDataNotFoundException;
use Magento\Catalog\Model\Category;
use Magento\Store\Model\StoreManagerInterface;

class ProductResolver implements ProductResolverInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @var array
     */
    private $products;
    
    /**
     * @param StoreManagerInterface $storeManager
     * @param array                 $products
     */
    public function __construct(StoreManagerInterface $storeManager, array $products)
    {
        $this->storeManager = $storeManager;
        $this->products = $products;
    }

    /**
     * @param  string   $urlKey
     *
     * @return EntityData
     */
    public function resolveByUrlKey(string $urlKey, int $storeId) : EntityData
    {
        foreach ($this->products as $product) {
            $urlKeys = $product->getData('url_key');
            $productUrlKeyInStore = isset($urlKeys[$storeId]) ? $urlKeys[$storeId] : $urlKeys[0];

            if ($productUrlKeyInStore == $urlKey) {
                return new EntityData('product', $product->getId(), $urlKey);
            }
        }

        throw new EntityDataNotFoundException('not found!');
    }

    /**
     * @param string $urlKey
     * @param int    $storeId
     *
     * @return EntityData[]
     */
    public function resolveAllByUrlKey(string $urlKey, int $storeId) : array
    {
        throw new \Exception(__METHOD__ . 'Method not implemented');
    }

    /**
     * @param  int    $productId
     *
     * @return EntityData
     */
    public function resolveById(int $productId, int $storeId) : EntityData
    {
        throw new \Exception(__METHOD__ . 'Method not implemented');
    }

    /**
     * @param  int    $productId
     *
     * @return int[]
     */
    public function resolveCategoryIds(int $productId, int $storeId) : array
    {
        $defaultIds = [
            Category::TREE_ROOT_ID,
            $this->storeManager->getStore()->getRootCategoryId()
        ];

        $product = $this->products[$productId];

        return array_merge($defaultIds, $product->getCategoryIds() ?: []);
    }
}
