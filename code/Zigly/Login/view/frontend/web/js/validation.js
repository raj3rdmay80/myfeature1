/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
require(
    [
        'Magento_Ui/js/lib/validation/validator',
        'jquery',
        'mage/translate'
    ],
    function(validator, $) {

        $('body').on("keypress", 'input[name="telephone"][type="text"], input[name="postcode"][type="text"]', function(e) {

            var keyC = e.keyCode;

            if ((keyC != 8 || keyC == 32) && (keyC < 48 || keyC > 57)) {
                return false;
            }

            //set the maximum length and minimum length
            $('input[name="telephone"][type="text"]').attr('maxlength', '10');
            $('input[name="telephone"][type="text"]').attr('minlength', '10');
            $('input[name="postcode"][type="text"]').attr('maxlength', '6');
            $('input[name="postcode"][type="text"]').attr('minlength', '6');
        });


        validator.addRule('pincode-validation', function(value) {
                return value.match(/^[1-9]{1}[0-9]{5}$/);
            }

            , $.mage.__('Enter your 6 digit Postal Code.'));

        validator.addRule('phonenumber-validation', function(value) {
            return value.match(/^[1-9]{1}[0-9]{9}$/);
        }, $.mage.__('Enter your 10 digit Mobile Number'));

    });