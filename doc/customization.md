# Customizations

##Best Practice
* Do not modify the plugin directly, or you will loose the benefit of easy updates!
* You can _either_ use Magento2 Plugins to extend the default functionality of the module in a virtually unrestricted way, by:
* Adding the plugin to Styla Connect in your own module, where in your di.xml you add:
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Styla\Connect2\Model\Product\Info\Renderer\DefaultRenderer">
        <plugin name="your-custom-renderer-data-processor" type="Namespace\Module\Model\Product\Info\Renderer\Plugin" sortOrder="1"/>
    </type>
</config>
```

* ...and then create a plugin class in your own module. In this example, we're allowing to modify the values returned for a product on the magazine page.
```php
<?php
namespace (...)

class Plugin
{
    public function afterRender(\Styla\Connect2\Model\Product\Info\Renderer\DefaultRenderer $subject, $result)
    {
        //do whatever you need with the result, and return it
    }
}
```
* More information about Magento2 plugins is available here: http://devdocs.magento.com/guides/v2.0/extension-dev-guide/plugins.html

* _or_, use some of the events available in the module: [Events](events.md)

## Events

* `styla_category_get_tree`
* `styla_get_product_collection`

You can use these events to easily manipulate the returned objects in the way you want. This is a less robust, but also somewhat less intrusive approach.
Recommended, if you only need to make a small change to the values returned by default.

A full list of events can be found [here](events.md). 
