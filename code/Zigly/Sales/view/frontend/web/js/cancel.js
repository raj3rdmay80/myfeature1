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
            $(document).on('click', '.order-cancel', function(event){
                event.preventDefault();
                var orderId = config.orderId;
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    showLoader: true,
                    title: 'Cancel Order',
                    buttons: [{
                       class: 'action-close',
                       click: function () {
                           this.closeModal();
                       }
                   }]
                };
                var popup = modal(options, jQuery('#popup-mpdal-cancel'));
                jQuery('#reason').val('');
                jQuery("#popup-mpdal-cancel").modal("openModal");
            });
            $(document).on('click', '.order-cancel-reason', function(e){
                var orderId = config.orderId;
                var cancelurl = config.AjaxUrl;
                var cancelreason = "";
                if(jQuery("#reason").val() == 'nothing'){
                    cancelreason = jQuery("#reasons").val();
                    if (cancelreason.length != 0){
                        e.preventDefault();
                        var ajax = jQuery.ajax({
                            url: cancelurl,
                            type: 'POST',
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                console.log(errorThrown);
                            },
                            data: {'orderid': orderId, 'cancelreason': cancelreason},
                            showLoader: true,
                            beforeSend: function(){
                            },
                            success: function (response) {
                                window.location.reload();
                            }
                        });;
                        return ajax;
                    } else {
                        jQuery(".error").html("<p class = 'error-msg'>This is a required field.</p>");
                        jQuery('.error-msg').css({ 'color': 'red'});
                        e.preventDefault();
                    }
                } else {
                    cancelreason = jQuery("#reason").val();
                    if (cancelreason.length != 0){
                        var nothing = jQuery("#reasons").val();
                        if(nothing.length != 0) {
                            cancelreason = jQuery("#reason").val() + ' - ' + nothing;
                        } else {
                            cancelreason = jQuery("#reason").val();
                        }
                        e.preventDefault();
                         jQuery.ajax({
                            url: cancelurl,
                            type: 'POST',
                            data: {'orderid': orderId, 'cancelreason': cancelreason},
                            showLoader: true,
                            beforeSend: function(){
                            },
                            success: function (response) {
                                window.location.reload();
                            }
                        });
                    }
                }
                jQuery('.modal-header .action-close').click(function(event) {
                    jQuery('.modals-wrapper').hide();
                });
            });
            $(document).on('click', '.booking-cancel', function(event){
                event.preventDefault();
                var bookingId = jQuery(this).attr('key');
                confirmation({
                    title: 'Cancel Booking',
                    content: "By submitting the request, you agree the <a href='"+config.policy+"' target='_blank'>cancellation policies</a>",
                    actions: {
                        confirm: function () {
                            window.location.href = config.bookingUrl+'/id/'+bookingId;
                        },
                        cancel: function () {}
                    }
                });
                return false;
            });
            $(document).on('click', '.return-order', function(event){
                event.preventDefault();
                var orderId = config.orderId;
                var returnOptions = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    showLoader: true,
                    title: 'Return Order',
                    buttons: [{
                       class: 'action-close',
                       click: function () {
                           this.closeModal();
                       }
                   }]
                };
                var popup = modal(returnOptions, jQuery('#popup-mpdal-return'));
                jQuery('#return').val('');
                jQuery("#popup-mpdal-return").modal("openModal");
            });
            $(document).on('click', '.order-return-reason', function(e){
                var orderId = config.orderId;
                var returnUrl = config.returnUrl;
                var returnReason = "";
                if(jQuery("#return").val() == 'nothing'){
                    returnReason = jQuery("#returns").val();
                    if (returnReason.length != 0){
                        e.preventDefault();
                        var ajax = jQuery.ajax({
                            url: returnUrl,
                            type: 'POST',
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                console.log(errorThrown);
                            },
                            data: {'orderid': orderId, 'return_reason': returnReason},
                            showLoader: true,
                            beforeSend: function(){
                            },
                            success: function (response) {
                                window.location.reload();
                            }
                        });;
                        return ajax;
                    } else {
                        jQuery(".error").html("<p class = 'error-msg'>This is a required field.</p>");
                        jQuery('.error-msg').css({ 'color': 'red'});
                        e.preventDefault();
                    }
                } else {
                    returnReason = jQuery("#return").val();
                    if (returnReason.length != 0){
                        var nothing = jQuery("#returns").val();
                        if(nothing.length != 0) {
                            returnReason = jQuery("#return").val() + ' - ' + nothing;
                        } else {
                            returnReason = jQuery("#return").val();
                        }
                        e.preventDefault();
                         jQuery.ajax({
                            url: returnUrl,
                            type: 'POST',
                            data: {'orderid': orderId, 'return_reason': returnReason},
                            showLoader: true,
                            beforeSend: function(){
                            },
                            success: function (response) {
                                window.location.reload();
                            }
                        });
                    }
                }
                jQuery('.modal-header .action-close').click(function(event) {
                    jQuery('.modals-wrapper').hide();
                });
            });
        });
    }
});
