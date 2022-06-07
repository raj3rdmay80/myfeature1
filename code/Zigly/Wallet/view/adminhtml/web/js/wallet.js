/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/modal'
], function ($, confirmation, modal) {
    'use strict';

    return function (config) {
        $(document).ready(function(){
            $(document).on('click', '#add-wallet-button', function(event){
                event.preventDefault();
                var options = {
                    type: 'slide',
                    responsive: true,
                    innerScroll: true,
                    showLoader: true,
                    title: 'Add Wallet Money',
                    buttons: [{
                        text: $.mage.__(),
                        class: 'modal-wallet',
                        click: function () {
                            this.closeModal();
                        }
                    }]
                };
                var popup = modal(options, jQuery('#popup-mpdal-wallet'));
                jQuery('#amount').val('');
                jQuery('.page-main-actions').css("display", "none");
                jQuery("#popup-mpdal-wallet").modal("openModal");
            });
            $(document).on('click', '#add-wallet', function(e){
                $("#errormsg").remove();
                var customerId = config.customerId;
                var ajaxUrl = config.AjaxUrl;
                let amount = "";
                if ($.isNumeric(jQuery("#amount").val())) {
                    amount = jQuery("#amount").val();
                }
                if (amount == "") {
                    $('<div class="message-error error message" id="errormsg">Amount is required.</div>').insertBefore('.choose-amount');
                    return false;
                }
                var flag = $("#flag").val();
                if (flag == 1) {
                    if (!(parseInt(amount) <= parseInt(config.maxTotal))) {
                        $('<div class="message-error error message" id="errormsg">Wallet recharge can be less than or equal to '+config.max+'.</div>').insertBefore('.choose-amount');
                        return false;
                    }
                    if (!(parseInt(amount) >= parseInt(config.minTotal))) {
                        $('<div class="message-error error message" id="errormsg">Wallet recharge can be greater than or equal to '+config.min+'.</div>').insertBefore('.choose-amount');
                        return false;
                    }
                }
                if (flag == "") {
                    $('<div class="message-error error message" id="errormsg">Please select credit/debit.</div>').insertBefore('.choose-amount');
                    return false;
                }
                if (flag == 0 && parseInt(amount) > parseInt(config.balance)) {
                    $('<div class="message-error error message" id="errormsg">We can\'t debit wallet amount more that available balance.</div>').insertBefore('.choose-amount');
                    return false;
                }
                var remarks = jQuery("#remarks").val();
                var comment = "Recharge Wallet";
                if (remarks != '') {
                    comment = remarks;
                } else if (flag == 0) {
                    comment = "Debited Wallet";
                }
                if(amount != ''){
                    e.preventDefault();
                    var ajax = jQuery.ajax({
                        url: ajaxUrl,
                        dataType: 'json',
                        type: 'POST',
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            setInterval(function(){ 
                                window.location.reload();
                            }, 4000);
                            jQuery("#popup-mpdal-wallet").modal("closeModal");
                            $('<div class="message message-error error">Can\'t able to recharge</div>').insertAfter('.page-main-actions');
                        },
                        data: {'customerid': customerId, 'amount': amount, 'comment': comment, 'flag': flag},
                        showLoader: true,
                        beforeSend: function(){
                        },
                        success: function (response) {
                            setInterval(function(){ 
                                window.location.reload();
                            }, 4000);
                            jQuery("#popup-mpdal-wallet").modal("closeModal");
                            if (response.success) {
                                $('<div class="message message-success success" id="errormsg">' + response.message + '</div>').insertAfter('.page-main-actions');
                            } else {
                                $('<div class="message-error error message" id="errormsg">' + response.message + '</div>').insertAfter('.page-main-actions');
                            }
                        }
                    });;
                    return ajax;
                }
            });
        });
    }
});
