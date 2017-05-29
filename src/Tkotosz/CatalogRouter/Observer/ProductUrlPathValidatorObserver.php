<?php

namespace Tkotosz\CatalogRouter\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Model\AbstractModel;
use Tkotosz\CatalogRouter\Observer\PathValidatorObserver;

class ProductUrlPathValidatorObserver extends PathValidatorObserver
{
    protected function getCurrentEntityType()
    {
        return 'product';
    }

    protected function getEntity(Observer $observer)
    {
        return $observer->getEvent()->getProduct();
    }

    protected function getEntityStoreIds(AbstractModel $entity)
    {
        return $entity->getStoreIds();
    }

    protected function getEntityUrlPath(AbstractModel $entity, int $storeId)
    {
        return $this->urlPathProvider->getProductUrlPath($entity->getId(), $storeId);
    }
}
