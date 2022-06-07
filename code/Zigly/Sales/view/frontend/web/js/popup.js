/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/modal'
], function ($, confirmation, modal) {
    'use strict';

    return function (config) {
        $(document).ready(function(){
            $(document).on('click', '#groomer-review-link', function() {
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    showLoader: true,
                    modalClass: 'review-feedback',
                    title: config.title,
                    buttons: []
                };
                $("#popup-modal-review-tag").modal(options).modal("openModal");
            });
        });
    }
});
