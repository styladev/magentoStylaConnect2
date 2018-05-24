# Configuration

* Access your admin panel (get URL by running `./bin/magento info:adminuri` from the Magento2 root folder)
* Go to **Stores** -> **Configuration**
* You should see the following:

![ConfigScreen](http://i.imgur.com/lk6pNzq.png)

* In order to complete the configuration click on `initialize connection` on top of the screen
* Enter your Styla credentials (provided by your account manager) and click "Connect to Styla"
* You should get redirected to the Magento Dashboard seeing the following message:

![SuccessMsg](http://imgur.com/GZ71BGD.png)

## General

<table>
<tr>
<th>Name</th>
<th>Description</th>
<th>Default</th>
</tr>

  
  <tr>
<td>Use Magento Layout on Magazine Pages</td>
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
    <td>Add Magazine Link to Navigation</td>
    <td>If enabled, will add a navigation button leading to your magazine in the main navigation tree of your store, next to the main categories.</td>
    <td>yes</td>
  </tr>
  
  <tr>
    <td>Label for the Magazine Menu Link</td>
    <td>If the "Add Magazine Link to Navigation" option is enabled, you can add a custom label for the magazine link, here.</td>
    <td>Magazine</td>
  </tr>

  <tr>
    <td>Enable the Module</td>
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
<td>Label for the Magazine Menu Link</td>
<td>The label used for the navigation menu link leading to the Magento page with your magazine embedded.</td>
<td>Magazine</td>
</tr>

<tr>
<td>Frontend Name (route)</td>
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
<td>Override Url for SEO Api (Developer Mode Only)</td>
<td>Developer mode only. Allows you to enter a custom URL for the SEO data.</td>
<td><i>empty</i>, disabled</td>
</tr>
  
<tr>
<td>Override Url for CDN/Assets (Developer Mode Only)</td>
<td>Developer mode only. Allows you to enter a custom URL for the magazine content provider.</td>
<td><i>empty</i>, disabled</td>
</tr>
  
<tr>
<td>Override Url for Styla Content Version API (Developer Mode Only)</td>
<td>Developer mode only. Allows you to enter a custom URL for magazine content version provider.</td>
<td><i>empty</i>, disabled</td>
</tr>
</table>

** The three following URLs should be used:  
 `https://client-scripts.styla.com`    
 `http://seoapi.styla.com`    
 `https://client-scripts.styla.com`     
as shown on this screen shot:
![Styla New JS source](/doc/styla-plugin-client-scripts-magento2.png)  
If different set, **please update them**, then switch Developer Mode dropdown to **OFF again** and click the **Save Config** button top-right in order to use them. 

## Turn off http password-protection on API endpoints

Styla sources product data from Magento REST API which is protected by OAuth. Our application cannot access the endpoints if they are in addition password-protected, which is a common solution for protecting development and stage environments on which the plugin is first installed.

If your stage environment is password protected, please turn it off for `http://yourdomain/rest/v1*` where the enddpoints are located. 

Alternatively, turn password-protection on your stage altogether for the time Styla is using it. 
