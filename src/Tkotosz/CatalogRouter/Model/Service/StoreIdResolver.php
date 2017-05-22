<?php

namespace Tkotosz\CatalogRouter\Model\Service;

use Magento\Catalog\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;

class StoreIdResolver
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }
    
    /**
     * Replicate the original magento behaviour for store is handling from the url model
     * 
     * @param AbstractModel $entity
     * @param array         $params
     *
     * @return int
     */
    public function resolve(AbstractModel $entity, array $params = []) : int
    {
        $storeId = $entity->getStoreId();

        if (isset($params['_scope'])) {
            $storeId = $this->storeManager->getStore($params['_scope'])->getId();
        }

        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $storeId;
    }
}
