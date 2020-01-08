# Configuration

* Access your admin panel (get URL by running `./bin/magento info:adminuri` from the Magento2 root folder)
* Go to **Stores** -> **Configuration**
* You should see the following:

![ConfigScreen](../doc/new-settings-after-2-0-9.png)

or this, in case of a pre-2.0.9 version:

![ConfigScreenOld](http://i.imgur.com/lk6pNzq.png)

* In order to complete the configuration click on `initialize connection` on top of the screen
* Enter your Styla credentials (provided by your account manager) and click "Connect to Styla"
* You should get redirected to the Magento Dashboard seeing the following message:

![SuccessMsg](http://imgur.com/GZ71BGD.png)

* In case an error message is displayed and you don't have the correct user name and/or pasword, contact Styla

## General

<table>
<tr>
<th>Name</th>
<th>Description</th>
<th>Default</th>
</tr>

  
  <tr>
<td>Use Magento Layout on Magazine Pages*</td>
<td>Showing the current magento theme around the magazine:

<ul>
<li>yes - the Styla magazine page will be wrapped within a regular Magento header and
footer.

</li>
<li>no - only the magazine will be visible, without any Magento theme or content applied

</li>
</ul>

</td>
<td>yes</td>
</tr>
  
  <tr>
    <td>Add Magazine Link to Navigation*</td>
    <td>If enabled, will add a navigation button leading to your magazine in the main navigation tree of your store, next to the main categories.</td>
    <td>yes</td>
  </tr>
  
  <tr>
    <td>Label for the Magazine Menu Link*</td>
    <td>If the "Add Magazine Link to Navigation" option is enabled, you can add a custom label for the magazine link, here.</td>
    <td>Magazine</td>
  </tr>

  <tr>
    <td>Enable the Module*</td>
    <td>Allows you to completely disable the module, if you need to.</td>
    <td>yes</td>
  </tr>

<tr>
<td>Use Relative Product Urls</td>
<td>Defines how product URLs for magazine front-end will be created:
* yes - the product urls generated for the stories will be relative to store domain (ie: /product-name-SKU/)
* no - no - full urls will be generated (ie: http://www.yourdomain.com/product-name-SKU/)</td>
<td>No</td>
</tr>

<tr>
<td>Label for the Magazine Menu Link*</td>
<td>The label used for the navigation menu link leading to the Magento page with your magazine embedded.</td>
<td>Magazine</td>
</tr>

<tr>
<td>Frontend Name (route)*</td>
<td>This is the public URL of your magazine, as in: http://yourstoreurl.com/{FRONTEND_NAME} <br/>This can be left empty, and will default to "magazin".</td>
<td>/magazine</td>
</tr>

<tr>
<td>Username*</td>
<td><i>This is filled by the Connection Manager during the automatic configuration. You shouldn't change this value.</i></td>
<td>*N/A*</td>
</tr>
  
<tr>
<td>Cache Lifetime</td>
<td>How long results taken from Styla CDN are stored locally, in seconds.</td>
<td>360</td>
</tr>
</table>  
  
## Developer Mode (advanced)
  
<table>

<tr>
<th>Name</th>
<th>Description</th>
<th>Default</th>
</tr>  

<tr>
<td>Use Developer Mode</td>
<td>Allows you to override certain default values used for retrieving data from Styla. <br/><i>You generally won't ever need to use this, and it's recommended to leave this option disabled.</i></td>
<td>no</td>
</tr>
  
<tr>
<td>Override Url for SEO Api (Developer Mode Only)**</td>
<td>Developer mode only. Allows you to enter a custom URL for the SEO data.</td>
<td><i>empty</i>, disabled</td>
</tr>
  
<tr>
<td>Override Url for CDN/Assets (Developer Mode Only)**</td>
<td>Developer mode only. Allows you to enter a custom URL for the magazine content provider.</td>
<td><i>empty</i>, disabled</td>
</tr>
  
<tr>
<td>Override Url for Styla Content Version API (Developer Mode Only)**</td>
<td>Developer mode only. Allows you to enter a custom URL for magazine content version provider.</td>
<td><i>empty</i>, disabled</td>
</tr>
</table>

* Moved to a separate Content > Styla magazines menu in the 2.0.9 version 

** For pre-2.0.9 versions, the three following URLs should be used:  
 `https://client-scripts.styla.com`    
 `http://seoapi.styla.com`    
 `https://client-scripts.styla.com` 
 
as shown on this screen shot:
![Styla New JS source](/doc/styla-plugin-client-scripts-magento2.png)  
If different set, **please update them**, then switch Developer Mode dropdown to **OFF again** and click the **Save Config** button top-right in order to use them. Only Styla JS sourced from `https://client-scripts.styla.com` can render Styla Landing Pages.

## Setting up Styla Magazines and Pages

The 2.0.9 version of the plugin introduced a separate menu in **Content > Styla magazines** to set up multiple pages with styla content on multiple store views. The fields marked with ** in the table above were moved to this new menu which looks like this: 
![Styla Magazines List](/doc/styla-plugin-client-scripts-magento2.png) 
When activating the plugin for the first time, you will only see the first entry on the list with the default Styla user name and /magazine as the default path.

You can add more entries on the list using different Styla user names to source the content from and setting up different paths for them in the **Front Name** field. 

Using the settings for each of the entries you can also deactivate it completely or keep it activated but switch off link to it in your Magento menu. 

### Styla on your homepage

You can have a specific Styla Page you create in Styla CMS at https://editor.styla.com embedded by the plugin on your homepage of a specific store view, so on the `/` path. In order to do this, please:

1. Publish the page with blank "Page URL Path" field, like this: 
![Styla Page set up to be displayed on home](/doc/styla-home-plugin.png) 

2. Create a new page in the in the **Content > Styla magazines** menu in Magento Backend and leave the "Front Name" field blank accordingly:
![The matching page in the Editor](/doc/styla-home-editor.png) 

The styla Page will now fill your main container on the home page, between the header and the footer. It will also have server-side rendered tags from Styla's SEO API in page source. 

## Turn off http password-protection on API endpoints

Styla sources product data from Magento REST API which is protected by OAuth. Our application cannot access the endpoints if they are in addition password-protected, which is a common solution for protecting development and stage environments on which the plugin is first installed.

If your stage environment is password protected, please turn it off for `http://yourdomain/rest/v1/*` where the enddpoints are located. 

Alternatively, turn password-protection on your stage altogether for the time Styla is using it. 

## SEO Content from Styla's SEO API

The module uses data from Styla's SEO API to:
* generate tags like: meta tags including `<title>`, canonical link, og:tags, static content inserted into <body>, `robots` instructions
* insert these tags accordingly into HTML of the template the page with Styla content uses
  
This is done to provide search engine bots with data to crawl and index all Styla URLs, which are in fact a Single-Page-Application.

Once you install and configure the module, please open source of the page on which your Styla content is embedded and check if none of the tags mentioned below are duplicated. In case `robots`or `link rel="canonical"` or any other are in the HTML twice, make sure to remove the original ones coming from your default template. Otherwise search engine bots might not be able to crawl all the Styla content or crawl it incorrectly. 

You can finde more information on the SEO API on [this page](https://docs.styla.com/seo-integration)
