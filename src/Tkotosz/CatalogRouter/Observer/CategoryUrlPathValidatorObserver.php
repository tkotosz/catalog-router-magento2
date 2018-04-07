<?php

namespace Tkotosz\CatalogRouter\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Model\AbstractModel;
use Tkotosz\CatalogRouter\Model\UrlPath;
use Tkotosz\CatalogRouter\Observer\PathValidatorObserver;

class CategoryUrlPathValidatorObserver extends PathValidatorObserver
{
    protected function getCurrentEntityType()
    {
        return 'category';
    }

    protected function getEntity(Observer $observer)
    {
        return $observer->getEvent()->getCategory();
    }

    protected function getEntityStoreIds(AbstractModel $entity)
    {
        $storeIds = [];

        foreach ($entity->getStoreIds() as $storeId) {
            if ($storeId == 0) {
                continue;
            }

            $storeIds[] = $storeId;
        }

        return $storeIds;
    }

    protected function getEntityUrlPath(AbstractModel $entity, int $storeId)
    {
        return $this->urlPathProvider->getCategoryUrlPath($entity->getId(), $storeId);
    }
}
