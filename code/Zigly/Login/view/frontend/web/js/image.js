require([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {

    //Validate Image Extensions
    $.validator.addMethod(
        'validate-fileextensions', function (v, elm) {
            var extensions = ['jpeg', 'jpg', 'png'];
            if (!v) {
                return true;
            }
            with (elm) {
                var ext = value.substring(value.lastIndexOf('.') + 1);
                for (i = 0; i < extensions.length; i++) {
                    if (ext == extensions[i]) {
                        return true;
                    }
                }
            }
            return false;
        }, $.mage.__('Allowed file types jpeg , jpg , png.'));

    /*function image() {
        document.getElementById('selected-profile-image').style.display = "block";
        document.getElementById('selected-profile-image').src = URL.createObjectURL(event.target.files[0]);
    }*/
});