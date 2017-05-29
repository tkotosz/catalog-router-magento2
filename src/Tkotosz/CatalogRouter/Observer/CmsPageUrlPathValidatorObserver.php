<?php

namespace Tkotosz\CatalogRouter\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Model\AbstractModel;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Observer\PathValidatorObserver;

class CmsPageUrlPathValidatorObserver extends PathValidatorObserver
{
    protected function getCurrentEntityType()
    {
        return 'cms page';
    }

    protected function getEntity(Observer $observer)
    {
        return $observer->getEvent()->getObject();
    }

    protected function getEntityStoreIds(AbstractModel $entity)
    {
        $stores = $entity->getStores();

        if ($stores == [0]) {
            $stores = array_keys($this->storeManager->getStores());
        }

        return $stores;
    }

    protected function getEntityUrlPath(AbstractModel $entity, int $storeId)
    {
        return new UrlPath($entity->getIdentifier());
    }
}
