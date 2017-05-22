<?php

namespace Tkotosz\CatalogRouter\Controller;

use Tkotosz\CatalogRouter\Model\Exception\CatalogEntityNotFoundException;
use Tkotosz\CatalogRouter\Model\Service\CatalogUrlPathResolver;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;
    
    /**
     * @var CatalogUrlPathResolver
     */
    private $catalogUrlPathResolver;
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ActionFactory          $actionFactory
     * @param CatalogUrlPathResolver $catalogUrlPathResolver
     * @param StoreManagerInterface  $storeManager
     */
    public function __construct(
        ActionFactory $actionFactory,
        CatalogUrlPathResolver $catalogUrlPathResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->actionFactory = $actionFactory;
        $this->catalogUrlPathResolver = $catalogUrlPathResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * @param RequestInterface $request
     * 
     * @return ActionInterface
     */
    public function match(RequestInterface $request)
    {
        if (!($request instanceof HttpRequest)) {
            return null;
        }

        if ($request->getControllerName()) {
            return null;
        }

        try {
            $urlPath = new UrlPath($request->getPathInfo());
            $entity = $this->catalogUrlPathResolver->resolve($urlPath, $this->storeManager->getStore()->getId());
            $request->setModuleName('catalog')
                ->setControllerName($entity->getType())
                ->setActionName('view')
                ->setParam('id', $entity->getId());
            
            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlPath->getIdentifier());
            $result = $this->actionFactory->create(Forward::class);
        } catch (CatalogEntityNotFoundException $e) {
            $result = null;
        }

        return $result;
    }
}
