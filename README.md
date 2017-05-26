Catalog Router module for Magento 2
=========================
[![License](https://poser.pugx.org/bex/behat-screenshot/license)](https://packagist.org/packages/tkotosz/catalog-router-magento2)
[![Latest Stable Version](https://poser.pugx.org/tkotosz/catalog-router-magento2/version)](https://packagist.org/packages/tkotosz/catalog-router-magento2)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tkotosz/catalog-router-magento2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tkotosz/catalog-router-magento2/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/tkotosz/catalog-router-magento2/badges/build.png?b=master)](https://scrutinizer-ci.com/g/tkotosz/catalog-router-magento2/build-status/master)

This module disables the functionality of the Catalog Url Rewrite module and takes over the responsibility of url rendering and routing (so all category and product url will be handled by this module). It still allows to manually create rewrites/redirects in the admin using the default Catalog Url Rewrite module, but it will disable the automatic rewrite/redirect generation.

Note that:
- it was only tested in magento 2.1.6 env
- it requires php7
- It doesn't support generating product urls with category path (it can do the routing already, but will always render direct product url without category path)
- It doesn't create 301 redirects automatically when changing product/category url key (so it needs to be created manually)

TODO:
- Generate 301 redirects automatically when url key changed
- Support generating product urls with category path
- Validate url key when saving entities (category/product/cmspage/etc) to see if it is available
