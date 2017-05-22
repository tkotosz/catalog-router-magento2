<?php

namespace Fixtures;

use Tkotosz\CatalogRouter\Api\ProductResolverInterface;
use Tkotosz\CatalogRouter\Model\CatalogEntity;
use Tkotosz\CatalogRouter\Model\Exception\CatalogEntityNotFoundException;
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
     * @return CatalogEntity
     */
    public function resolveByUrlKey(string $urlKey, int $storeId) : CatalogEntity
    {
        foreach ($this->products as $product) {
            $urlKeys = $product->getData('url_key');
            $productUrlKeyInStore = isset($urlKeys[$storeId]) ? $urlKeys[$storeId] : $urlKeys[0];

            if ($productUrlKeyInStore == $urlKey) {
                return new CatalogEntity('product', $product->getId(), $urlKey);
            }
        }

        throw new CatalogEntityNotFoundException('not found!');
    }

    /**
     * @param  int    $productId
     *
     * @return CatalogEntity
     */
    public function resolveById(int $productId, int $storeId) : CatalogEntity
    {
        throw new CatalogEntityNotFoundException();
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
