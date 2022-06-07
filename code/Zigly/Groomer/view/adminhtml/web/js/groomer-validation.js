require(
    [
        'Magento_Ui/js/lib/validation/validator',
        'jquery',
        'mage/translate'
    ], function(validator, $){

        validator.addRule(
            'pincode-validation',
            function (value) {
                if (!value) {
                    return true;
                }
                return value.match(/^[1-9]{1}[0-9]{5}$/);
            }
            ,$.mage.__('Please enter a valid pincode.')
        );

        validator.addRule(
            'name-validation',
            function (value) {
                if (!value) {
                    return true;
                }
                return value.match(/^[a-zA-Z\s]*$/);
            }
            ,$.mage.__('Please enter a valid full name.')
        );

        validator.addRule(
            'ifsc-validation',
            function (value) {
                if (!value) {
                    return true;
                }
                return value.match(/^[A-Z]{4}[0]{1}[A-Z0-9]{6}$/);
            }
            ,$.mage.__('Please enter a valid IFSC Code.')
        );
});