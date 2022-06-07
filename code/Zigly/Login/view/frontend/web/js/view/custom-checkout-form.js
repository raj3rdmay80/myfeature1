/*global define*/

define([
    'jquery',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/model/customer',
    'Magento_Ui/js/lib/validation/validator',
    'mage/url',
], function($,Component, customer, validator, url)  {
    'use strict';
    
    return Component.extend({

        userkeydown: function (event) {
            // Allow special chars + arrows 
            jQuery(".otpclass").keyup(function() {
                if (this.value.length == this.maxLength) {
                  var $next = jQuery(this).next('.otpclass');
                  if ($next.length) {
                    jQuery(this).next('.otpclass').focus();
                  } else {
                    jQuery(this).blur();
                  }
                }
                //***** Delete Check *****//
                if (this.value.length == 0)
                jQuery(this).prev('.otpclass').focus();
              });
            
        },

        initialize: function () {
            

            this._super();
            jQuery('body').on("keypress", 'input[name="mobilenumber"][type="text"], input[name="telephone"][type="text"], input[name="postcode"][type="text"]', function(e) {

                var keyC = e.keyCode; 
    
                if ((keyC != 8 || keyC == 32) && (keyC < 48 || keyC > 57)) {
                    return false;
                }
    
                //set the maximum length and minimum length
                jQuery('input[name="mobilenumber"][type="text"]').attr('maxlength', '10');
                jQuery('input[name="mobilenumber"][type="text"]').attr('minlength', '10');
                jQuery('input[name="telephone"][type="text"]').attr('maxlength', '10');
                jQuery('input[name="telephone"][type="text"]').attr('minlength', '10');
                jQuery('input[name="postcode"][type="text"]').attr('maxlength', '6');
                jQuery('input[name="postcode"][type="text"]').attr('minlength', '6');
                
            });
            $('body').on("keypress", 'input[name="firstname"][type="text"],input[name="lastname"][type="text"]', function(e) {
                    var regex = new RegExp("^[a-zA-Z]+$");
                     var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                     if (!regex.test(key)) {
                        event.preventDefault();
                        return false;
                     }
            });
    
            validator.addRule('phonenumber-validation', function(value) {
                return value.match(/^[1-9]{1}[0-9]{9}$/);
            }, jQuery.mage.__('Enter your 10 digit Mobile Number'));

            validator.addRule('pincode-validation', function(value) {
                return value.match(/^[1-9]{1}[0-9]{5}$/);
            }, jQuery.mage.__('Enter your 6 digit Postal Code.'));

          

            // component initialization logic
            return this;
        },

        isLoggedIn: function () {
            return customer.isLoggedIn();
        },

        /**
         * Form submit handler
         *
         * This method can have any name.
         */
        getFormKey: function () {
            return window.checkoutConfig.formKey;
        },

        isActive: function () {
            return !customer.isLoggedIn();
        },

        
        onSubmit: function() {

            var mobile = jQuery("#mobile").val();
            // jQuery('input[name="telephone"]').val(mobile);
            // jQuery('input[name="telephone"]').val(mobile);
            // jQuery('input[name="telephone"]').prop('disabled', true);
            // jQuery('input[name="username"]').prop('disabled', true);
            jQuery('input[name="telephone"]').prop('required',false);
            jQuery('input[id="customer-email"]').prop("placeholder", "Enter Email");
            jQuery('#errormsg').remove();
            //jQuery("input").prop('disabled', false);
            if(mobile=='')
            {

                jQuery('#errmsgf').remove();
                jQuery('<div class="message-error error message groom" id="errmsgf">Please enter 10 digit mobile number</div>').insertBefore('#otpForm');
                setTimeout(function() { jQuery("#errmsgf").hide(); }, 1000);    
            }
            else if(!jQuery('#mobile').val().match('[0-9]{10}'))
            {
                jQuery('#errmsgf').remove();
                jQuery('<div class="message-error error message groom" id="errmsgf">Please enter 10 digit mobile number</div>').insertBefore('#otpForm');
                setTimeout(function() { jQuery("#errmsgf").hide(); }, 1000);    
            } 
            else
            {
                var isApprovedCustomerUrl = url.build('login/customer/isapprovedcustomer');
                jQuery.ajax({
                        url: isApprovedCustomerUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: {username: mobile},
                        complete: function(response) {
                            jQuery('#successmsg').remove();
                            if(response.responseJSON.is_approved && response.responseJSON.existing_customer && response.responseJSON.msg == 'success' || !response.responseJSON.is_approved && !response.responseJSON.existing_customer && response.responseJSON.msg == 'success')
                            {
                                var sendotpmailurl = url.build('login/otp/sendotp');
                                jQuery.ajax({
                                    url: sendotpmailurl,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {username: mobile},
                                    complete: function(response) {
                                        jQuery("#otpForm,.alert-success").show();
                                        // jQuery("#mobileotp").hide();
                                        jQuery('#successmsg').remove();
                                        jQuery('<div class="message-success success message groom" id="successmsg">'+response.responseJSON.msg+'.</div>').insertBefore('#otpForm');
                                        setTimeout(function() { jQuery("#successmsg").hide(); }, 1000);
    
                                        //if(response.responseJSON.otp != 0){
                                        //setTimeout(function(){
                                        jQuery('.resendotpblock').show();
                                        jQuery('#resendotp').css('cursor','default');
                                    },
                                error: function (xhr, status, errorThrown) {
                                    console.log('Error happens. Try again.');
                                }
                                });
   
                        }
                        if(!response.responseJSON.is_approved && response.responseJSON.existing_customer && response.responseJSON.msg != 'success'){
                            jQuery('#errormsgunapproved').remove();
                            jQuery('<div class="message-error error message groom" id="errormsgunapproved">'+response.responseJSON.msg+'</div>').insertBefore('#otpForm');
                            setTimeout(function() { jQuery("#errormsgunapproved").hide(); }, 1000);
                        }
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                    }
                });

                
            }
        },

        
       

        
        onVerify: function() {

            
            
            
            var mobile = jQuery("#mobile").val();
            var formkey = jQuery("#formkey").val();

            var otp1 = jQuery("#otp1").val();
            var otp2 = jQuery("#otp2").val();
            var otp3 = jQuery("#otp3").val();
            var otp4 = jQuery("#otp4").val();

           //  alert(otp1 + otp2 + otp3 + otp4);
            var verifyotps = (otp1 + otp2 + otp3 + otp4);

            //alert(verifyotps);    
            // var verifyotps = jQuery("#verify_otp").val();
            // alert(verifyotps);
            
            let verifyData = {
                username: mobile,
                otp: verifyotps
            };
           
            // alert(verifyData);

            
            var otpverifyurl = url.build('login/verify/verifyotp');
            var logindataurl = url.build('customer/ajax/login/');
            var checkuserexisting = url.build('login/verify/checkexistuser');
            
            let arr = { form_key: formkey, username: mobile, login_pass_1: otp1, login_pass_2: otp2, login_pass_3: otp3, login_pass_4: otp4, password: verifyotps };
            
            jQuery.ajax({
                url: otpverifyurl,
                type: 'POST',
                dataType: 'json',
                data : verifyData,
                complete: function(response){
                    //alert("Success");
                    jQuery("#errormsg").remove();
                    if (response.responseJSON.status == 0) {
                        jQuery("#errormsg").remove();
                        jQuery('<div class="message-error error message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('#otpForm');
                        setTimeout(function() { jQuery("#errormsg").hide(); }, 1000);
                        jQuery("#otpForm").show();
                        
                    } else {
                        // jQuery.cookie("mage-cache-sessid", true);
                        jQuery("#sucmsgs").hide();
                        jQuery('<div class="message-success success message groom" id="sucmsgs">'+response.responseJSON.msg+'.</div>').insertBefore('#otpForm');
                        setTimeout(function() { jQuery("#sucmsgs").hide(); }, 1000);
                        jQuery("#otpForm").hide();
                        // jQuery(".amtheme-checkout-contact").hide();
                 
                         jQuery.ajax({
                            url: checkuserexisting,
                            type: 'POST',
                            data : arr,
                            dataType: 'json',
                            complete: function(response){
                                if (response.responseJSON == null) {
                                   
                                    var mobile = jQuery("#mobile").val();
                                    
                                    // jQuery('input[name="telephone"]').val(mobile);
                                   //  jQuery('input[name="telephone"]').prop('disabled', true);
                                    // jQuery('input[name="telephone"]').attr('readonly', true);
                                    $('input[name="telephone"][type="text"]').val($('#mobile').val());
                                    $('input[name="telephone"][type="text"]').change();
                                    jQuery('.amtheme-checkout-contact .step-title').css('display','none');
                                    jQuery('.customlogin').css('display','none');
                                    jQuery('<div class="message-success success message groom sucmsgsidf" id="sucmsgsidf"> Your Number '+mobile+' successfully Verified.</div>').insertBefore('.form-shipping-address'); 
                                    
                                    // jQuery('.amtheme-checkout-contact .step-title').css('display','none');
                                    localStorage.setItem("otpverified", mobile);


                                                        
                                } else {

                                    var mobile = jQuery("#mobile").val();
                                    // jQuery('input[name="telephone"]').val(mobile);

                                    
                                    // jQuery('input[name="telephone"]').prop('disabled', true);
                                    // jQuery('input[name="telephone"]').attr('readonly', true);
                                    jQuery('.amtheme-checkout-contact .step-title').css('display','none');
                                    jQuery('.customlogin').css('display','none');
                                    //jQuery('<div class="message-error error message groom sucmsgsidf" id="sucmsgsi">'+response.responseJSON.msg+'.</div>').insertBefore('.form-shipping-address');
                                    //jQuery('<div class="message-success success message groom" id="sucmsgsids"> Your Number '+mobile+' successfully Verified.</div>').insertBefore('.form-shipping-address');
                                    localStorage.setItem("otpverified", mobile)

                                    /*login the existing customer*/    
                                    var isCustomerLogin = url.build('login/customer/customerlogin');
                                    jQuery('body').trigger('processStart');
                                    jQuery.ajax({
                                            url: isCustomerLogin,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: arr,
                                            complete: function(response) {
                                                if(response.responseJSON.msg == 'success' && response.responseJSON.status){
                                                    location.reload();                
                                                }else{
                                                    $('body').trigger('processStop');
                                                    jQuery('#errormsgcustomerlogin').remove();
                                                    jQuery('<div class="message-error error message groom" id="errormsgcustomerlogin">'+response.responseJSON.msg+'.</div>').insertBefore('#otpForm');
                                                   setTimeout(function() { jQuery("#errormsgcustomerlogin").hide(); }, 1000); 
                                                }

                                        },
                                        error: function (xhr, status, errorThrown) {
                                             $('body').trigger('processStop');
                                            console.log('Error happens. Try again.');
                                        }
                                    });
                                    //setTimeout(function() { jQuery("#sucmsgsi").hide(); }, 1000);
                                }
                            },
                            error: function (xhr, status, errorThrown) {
                                console.log('Error happens. Try again.');
                            }
                        }); 
                }
            },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        },

    });
});
