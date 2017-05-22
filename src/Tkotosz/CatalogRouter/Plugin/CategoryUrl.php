<?php

namespace Tkotosz\CatalogRouter\Plugin;

use Tkotosz\CatalogRouter\Api\CatalogUrlProviderInterface;
use Tkotosz\CatalogRouter\Model\Exception\CatalogEntityNotFoundException;
use Tkotosz\CatalogRouter\Model\Service\StoreIdResolver;
use Magento\Catalog\Model\Category;
use Closure;

class CategoryUrl
{
    /**
     * @var CatalogUrlProviderInterface
     */
    private $catalogUrlProvider;
    
    /**
     * @var StoreIdResolver
     */
    private $storeIdResolver;
    
    /**
     * @param CatalogUrlProviderInterface $catalogUrlProvider
     * @param StoreIdResolver             $storeIdResolver
     */
    public function __construct(CatalogUrlProviderInterface $catalogUrlProvider, StoreIdResolver $storeIdResolver)
    {
        $this->catalogUrlProvider = $catalogUrlProvider;
        $this->storeIdResolver = $storeIdResolver;
    }
    
    /**
     * @param Category $category
     * @param Closure  $proceed
     *
     * @return string
     */
    public function aroundGetUrl(Category $category, Closure $proceed)
    {
        try {
            $storeId = $this->storeIdResolver->resolve($category);
            $url = $this->catalogUrlProvider->getCategoryUrl($category->getId(), $storeId);
        } catch (CatalogEntityNotFoundException $e) {
            $url = $proceed();
        }

        return $url;
    }
}
