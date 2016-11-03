# Event List
---

There is a number of events you can use in case you need to change any default values returned by the API.

<table>
<tr>
<th>Event Name</th>
<th>Dispatched when...</th>
</tr>
  
  <tr>
    <td>styla_category_get_tree</td>
    <td>A category tree is ready and filled with data</td>
  </tr>
  
  <tr>
    <td>styla_get_product_collection</td>
    <td>A collection of products requested by Styla API is processed and ready to be returned</td>
  </tr>
  
   <tr>
    <td>styla_data_convert_before</td>
    <td>Magento catalog data is about to be converted to fields needed by Styla API</td>
  </tr>
  
   <tr>
    <td>styla_data_convert_after</td>
    <td>Magento catalog data has just been converted to fields needed by Styla API</td>
  </tr>
  
  
   <tr>
    <td>styla_get_data_converters</td>
    <td>Get a list of converters needed for generating data format required by the Styla API</td>
  </tr>
  
     <tr>
    <td>styla_product_info_renderer_collect_additional</td>
    <td>Can be used to modify data of a product when it's requested on the magazine page</td>
  </tr>
  
</table>