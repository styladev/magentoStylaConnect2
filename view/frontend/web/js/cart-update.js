define([
    'Magento_Customer/js/customer-data'
], function(customerData) {
    'use strict';
    
    //the custom trigger that allows us to force reloading the minicart from any
    //script that needs this
    //by default, only an ajaxComplete event would trigger this (see customer-data.js for reference)
    window.stylaUpdateCart = function() {
        var sections = ["messages", "cart"];
        
        customerData.invalidate(sections);
        customerData.reload(sections, true);
    }
});
