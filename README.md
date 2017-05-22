Catalog Router module for Magento 2
=========================
[![License](https://poser.pugx.org/bex/behat-screenshot/license)](https://packagist.org/packages/tkotosz/catalog-router-magento2)
[![Latest Stable Version](https://poser.pugx.org/tkotosz/catalog-router-magento2/version)](https://packagist.org/packages/tkotosz/catalog-router-magento2)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tkotosz/catalog-router-magento2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tkotosz/catalog-router-magento2/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/tkotosz/catalog-router-magento2/badges/build.png?b=master)](https://scrutinizer-ci.com/g/tkotosz/catalog-router-magento2/build-status/master)

This module disables the functionality of the Catalog Url Rewrite module. It still allows to manually create rewrites/redirects in the admin using that module. But it will disable the automatic rewrite/redirect generation.
Instead of that this module will take over the responsibility of catalog url routing, so all category and product url will be handled by this module. (Including url rendering and routing).
The module was tested wtih single store, multi store with different category roots, multi store with the same category roots (see behat tests), but the development is still in progress.

Note that:
- it was only tested in magento 2.1.6 env
- it requires php7
- this module is in development and *not* ready for production use.
- It doesn't support generating product urls with category path (it can do the routing already, but will always render direct product url without category path)
- It doesn't create 301 redirects automatically when changing product/category url key (so it needs to be created manually)

TODO:
- Generate 301 redirects automatically when url key changed
- Support generating product urls with category path
