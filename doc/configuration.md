# Configuration
* Login into your magento backend
* Navigate to "Stores -> Configuration" and open the section "Styla Connect2"
* On the top of this configuration section, you'll always see a link to the "initialize connection" Connect Assistant. Click on it to grant access to your Magento installation for Styla.

## Connect Assistant

The Connect Assistant is *required* to run before you can use the magazine, as it will fill in some of the essential configuration settings. This action cannot be skipped and done manually.

The assistant will need your Styla email and password, and will automatically retrieve all the needed configuration data directly from Styla and create the configuration needed for accessing the magazine from within your Magento instance.
The assistant will create a new Magento2 "Api Integration" for the future access to the REST Api. You can see it in the "System -> Integrations" section.

## Configuration Values

<table>
<tr>
<th>Name</th>
<th>Description</th>
<th>Default</th>
</tr>
  
  <tr>
    <td>Enable the Module</td>
    <td>Allows you to completely disable the module, if you need to.</td>
    <td>yes</td>
  </tr>
  
  <tr>
<td>Use Magento Layout</td>
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
<td>Frontend Name (route)</td>
<td>This is the public URL of your magazine, as in: http://yourstoreurl.com/{FRONTEND_NAME} <br/>This can be left empty, and will default to "magazin".</td>
<td>/magazin</td>
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
  
  <tr>
<td>Developer  Mode*</td>
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

** Please do not modify these values. This configuration will be automatically set during the “Styla Connect” process from the previous step.

