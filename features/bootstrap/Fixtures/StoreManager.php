<?php

namespace Fixtures;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObject as Store;

class StoreManager implements StoreManagerInterface
{
    /**
     * @var array
     */
    private $stores;
    
    /**
     * @var Store
     */
    private $currentStore;
    
    /**
     * @param array $stores
     * @param Store $currentStore
     */
    public function __construct(array $stores, Store $currentStore)
    {
        $this->stores = $stores;
        $this->currentStore = $currentStore;
    }
       
    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     * @return void
     */
    public function setIsSingleStoreModeAllowed($value)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Check if store has only one store view
     *
     * @return bool
     */
    public function hasSingleStore()
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Retrieve application store object
     *
     * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore($storeId = null)
    {
        if (is_null($storeId)) {
            return $this->currentStore;
        }
        
        return $this->stores[$storeId];
    }

    /**
     * Retrieve stores array
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getStores($withDefault = false, $codeKey = false)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|\Magento\Store\Api\Data\WebsiteInterface $websiteId
     * @return \Magento\Store\Api\Data\WebsiteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWebsite($websiteId = null)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Get loaded websites
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return \Magento\Store\Api\Data\WebsiteInterface[]
     */
    public function getWebsites($withDefault = false, $codeKey = false)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Reinitialize store list
     *
     * @return void
     */
    public function reinitStores()
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Retrieve default store for default group and website
     *
     * @return \Magento\Store\Api\Data\StoreInterface|null
     */
    public function getDefaultStoreView()
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Retrieve application store group object
     *
     * @param null|\Magento\Store\Api\Data\GroupInterface|string $groupId
     * @return \Magento\Store\Api\Data\GroupInterface
     */
    public function getGroup($groupId = null)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Prepare array of store groups
     *
     * @param bool $withDefault
     * @return \Magento\Store\Api\Data\GroupInterface[]
     */
    public function getGroups($withDefault = false)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Set current default store
     *
     * @param string $store
     * @return void
     */
    public function setCurrentStore($store)
    {
        throw new \Exception('Method not implemented');
    }
}
