# Installation

As Magento 2 Supports composer based installations run
the following commands from your magento root directory
 
`composer require styla/magento2-connect`

After a successful installation you must enable the module with:

`php bin/magento module:enable Styla_Connect2`

Finally make sure everything is up to date with:

`php bin/magento setup:upgrade`

*Now, before you can start using your magazine - you must run the "Connection Assistant", available in the Styla Connect section of Magento configuration page.*
More information is available here: [Configuration](configuration.md)

### Please do not create any subpages in your CMS or directories for your magazine. The plugin itself will take care of setting up the /magazine/ (or any other) page on which the magazine will appear and of the roouting as well. 
