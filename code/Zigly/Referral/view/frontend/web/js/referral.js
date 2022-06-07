/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Referral
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return function (config) {
        $(document).ready(function(){
            $(document).on('click', '.invite-with-sms', function(event){
                event.preventDefault();
                $("#errormsg").remove();
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    showLoader: true,
                    title: 'Share With Friends',
                    buttons: [{
                        text: $.mage.__(),
                        class: 'modal-share-invite',
                        click: function () {
                            this.closeModal();
                        }
                    }]
                };
                var popup = modal(options, $('#popup-mpdal-invite'));
                $("input[name='phone_numbers']").val('');
                $('.modal-share-invite').css("display", "none");
                $("#popup-mpdal-invite").modal("openModal");
            });
            $(document).on('click', '#send-invite', function(e){
                e.preventDefault();
                $("#errormsg").remove();
                var ajaxUrl = config.AjaxUrl;
                var referral = config.referral;
                var number = $("#phone_numbers").val();
                if(number != ''){
                    console.log('test');
                    console.log(number);
                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            setInterval(function(){ 
                                window.location.reload();
                            }, 3000);
                            $("#popup-mpdal-invite").modal("closeModal");
                            $('<div class="message message-error error" id="errormsg">Can\'t able to send invite</div>').insertAfter('.page-main-actions');
                        },
                        data: {'number': number, 'referral': referral},
                        showLoader: true,
                        beforeSend: function(){
                        },
                        success: function (response) {
                            window.location.reload();
                        }
                    });
                } else {
                    $("#popup-mpdal-invite").modal("openModal");
                    $('<div class="message message-error error" id="errormsg">This is a required field.</div>').insertAfter('#phone_numbers');
                }
            });
        });
    }
});
