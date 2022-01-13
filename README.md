# Styla Connect 2 [![Latest Stable Version](https://poser.pugx.org/styla/magento2-connect/v/stable)](https://packagist.org/packages/styla/magento2-connect)
---

Requires: 
* PHP >= 5.4
* Magento 2, for our Magento 1 plugin check this https://github.com/styladev/magentoStylaConnect
* Magento REST API activated to share product information http://devdocs.magento.com/guides/v2.0/rest/bk-rest.html

Styla Connect is a module to connect your Magento 2 Store with [Styla](http://www.styla.com/) by embedding Styla content on a specific path and providing a source of product data. [This documentation page](https://docs.styla.com/) should provide you an overview of how Styla works in general. 

* [Installation](doc/installation.md)
* [Configuration](doc/configuration.md)
* [Customization](doc/customization.md)
* [Event List](doc/events.md)

## Setup Process

The process of setting up your Content Hub(s) usually goes as follows:

1. Install and configure the plugin on your stage using Content Hub ID(s) shared by Styla
2. Share the stage URL, credentials with Styla
4. Styla integrates product data from Magento REST API, test your stage Content Hub and asks additional questions, if needed
5. Install and configure the plugin on production, without linking to the Content Hub(s) there and, again, share the URL with Styla
6. Make sure your content is ready to go live
7. Styla conducts final User Acceptance Tests before the go-live
8. Go-live (you link to the Content Hub embedded on your production)

**Important notes**: 
* When updating from any previous version to 2.1.0 or higher, please let Styla know beforehand. Your settings need to be updated so that everything works fine.
* Version 2.2.0 fixes a problem happening on Magento 2.4 and above. If you are switching to this plugin version from any older than 2.1.0 please also let Styla know beforehand and make sure to test Styla content on a stage environment vefore releasing the updates to your production.
