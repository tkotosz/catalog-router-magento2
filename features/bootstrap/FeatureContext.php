<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Call\AfterScenario;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Fixtures\CategoryResolver;
use Fixtures\ProductResolver;
use Fixtures\StoreManager;
use Tkotosz\CatalogRouter\Model\Exception\CatalogEntityNotFoundException;
use Tkotosz\CatalogRouter\Model\Service\CatalogUrlPathResolver;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Magento\Framework\DataObject as Category;
use Magento\Framework\DataObject as Product;
use Magento\Framework\DataObject as Store;

class FeatureContext implements Context, SnippetAcceptingContext
{
    private $stores = [];
    private $categories = [];
    private $products = [];

    public function __construct()
    {
        $this->init();
    }

    /**
      * @AfterScenario
      */
     public function init()
     {
        $this->stores = [0 => new Store(['name' => 'Default', 'id' => 0])];
        $this->categories = [0 => new Category(['name' => 'RootRoot', 'id' => 0])];
        $this->products = [];
     }

     /**
     * @Transform :category
     * @Transform :parentCategory
     */
    public function transformStringToCategory($category)
    {
        foreach ($this->categories as $categoryObj) {
            if ($categoryObj->getName() == $category) {
                return $categoryObj;
            }
        }
        
        throw new \InvalidArgumentException('Category with name "' . $category . '" not found');
    }

    /**
     * @Transform :product
     */
    public function transformStringToProduct($product)
    {
        foreach ($this->products as $productObj) {
            if ($productObj->getName() == $product) {
                return $productObj;
            }
        }
        
        throw new \InvalidArgumentException('Product with name "' . $product . '" not found');
    }

    /**
     * @Transform :categories
     */
    public function transformCategoryNameListToCategoryList($categories)
    {
        $categoryObjs = [];
        foreach (explode(',', $categories) as $categoryName) {
            $categoryObjs[] = $this->transformStringToCategory(trim($categoryName));
        }
        return $categoryObjs;
    }

    /**
     * @Transform :store
     */
    public function transformStringToStore($store)
    {
        foreach ($this->stores as $storeObj) {
            if ($storeObj->getName() == $store) {
                return $storeObj;
            }
        }
        
        throw new \InvalidArgumentException('Store with name "' . $store . '" not found');
    }

    /**
     * @Transform :urlPath
     */
    public function transformStringToUrlPath($urlPath)
    {
        return new UrlPath($urlPath);
    }

     /**
     * @Given I have a store called :storeName
     */
    public function iHaveAStoreCalled($storeName)
    {
        $storeId = count($this->stores);
        $this->stores[$storeId] = new Store(['name' => $storeName, 'id' => $storeId]);
    }

    /**
     * @Given I have a root category called :rootCategoryName
     */
    public function iHaveARootCategoryCalled($rootCategoryName)
    {
        $categoryId = count($this->categories);
        $this->categories[$categoryId] = new Category(['id' => $categoryId, 'name' => $rootCategoryName, 'parent_id' => 0]);
    }

    /**
     * @Given I have a category called :categoryName under :parentCategory with this configuration:
     */
    public function iHaveACategoryCalledUnderWithThisConfiguration($categoryName, Category $parentCategory, TableNode $table)
    {
        $categoryId = count($this->categories);
        $data = ['id' => $categoryId, 'name' => $categoryName, 'parent_id' => $parentCategory->getId()];

        foreach ($table->getColumnsHash() as $attribute) {
            $attrName = $attribute['attribute_name'];
            $store = $this->transformStringToStore($attribute['store_level']);
            $attrValue = $attribute['attribute_value'];
            if ($attrValue !== 'use default') {
                $data[$attrName][$store->getId()] = $attrValue;
            }
        }

        $this->categories[$categoryId] = new Category($data);
    }

    /**
     * @Given I have a product called :productName with this configuration:
     */
    public function iHaveAProductCalledWithThisConfiguration($productName, TableNode $table)
    {
        $productId = count($this->products);
        $data = ['id' => $productId, 'name' => $productName];

        foreach ($table->getColumnsHash() as $attribute) {
            $attrName = $attribute['attribute_name'];
            $store = $this->transformStringToStore($attribute['store_level']);
            $attrValue = $attribute['attribute_value'];
            if ($attrValue !== 'use default') {
                $data[$attrName][$store->getId()] = $attrValue;
            }
        }

        $product = new Product($data);

        $this->products[$productId] = $product;

        return $product;
    }

    /**
     * @Given I have a product called :productName assigned to [:categories] categories with this configuration:
     */
    public function iHaveAProductCalledAssignedToCategoriesWithThisConfiguration($productName, array $categories, TableNode $table)
    {
        $product = $this->iHaveAProductCalledWithThisConfiguration($productName, $table);
        
        $categoryIds = [];
        
        foreach ($categories as $category) {
            $categoryIds[] = $category->getId();
        }

        $product->setData('category_ids', $categoryIds);
    }

    /**
     * @Given The :category root category is assigned to the :store
     */
    public function theRootCategoryIsAssignedToThe(Category $category, Store $store)
    {
        $store->setData('root_category_id', $category->getId());
    }

    /**
     * @When I visit the :urlPath page in the :store
     */
    public function iVisitThePageInThe(UrlPath $urlPath, Store $store)
    {
        $storeManager = new StoreManager($this->stores, $store);
        $categoryResolver = new CategoryResolver($this->categories);
        $productResolver = new ProductResolver($storeManager, $this->products);
        $this->urlPathResolver = new CatalogUrlPathResolver($categoryResolver, $productResolver, $storeManager);
        $this->currentUrlPath = $urlPath;
        $this->currentStore = $store;
    }

    /**
     * @Then I should see the :category category page
     */
    public function iShouldSeeTheCategoryPage(Category $category)
    {
        try {
            $resolvedCategory = $this->urlPathResolver->resolve($this->currentUrlPath, $this->currentStore->getId());
            if ($resolvedCategory->getId() !== $category->getId()) {
                throw new \Exception(sprintf("Expected category '%s' but got '%s'", $category->getId(), $resolvedCategory->getId()));
            }
        } catch (CatalogEntityNotFoundException $e) {
            throw new \Exception('Url was not resolved to any category');
        }
    }

    /**
     * @Then I should see the :product product page
     */
    public function iShouldSeeTheProductPage(Product $product)
    {
        try {
            $resolvedProduct = $this->urlPathResolver->resolve($this->currentUrlPath, $this->currentStore->getId());
            if ($resolvedProduct->getId() !== $product->getId()) {
                throw new \Exception(sprintf("Expected product '%s' but got '%s'", $product->getId(), $resolvedProduct->getId()));
            }
        } catch (CatalogEntityNotFoundException $e) {
            throw new \Exception('Url was not resolved to any product');
        }
    }

    /**
     * @Then I should see the 404 page
     */
    public function iShouldSeeThe404Page()
    {
        try {
            $resolvedCategory = $this->urlPathResolver->resolve($this->currentUrlPath, $this->currentStore->getId());
            throw new \Exception(sprintf("Url was not resolved to '%s' category", $resolvedCategory->getId()));
        } catch (CatalogEntityNotFoundException $e) {
            // everything ok, url was not resolved aka 404
        }
    }
}
