/**
 * Copyright (C) 2020  Zigly


 * @package Zigly_VetConsulting
 */
define([
    'jquery'
], function($) {
    'use strict';

    $(document).ready(function (){
        setTimeout(function() {
            console.log(require('Magento_Customer/js/customer-data').reload(['customer']))
        }, 600);
    });
});