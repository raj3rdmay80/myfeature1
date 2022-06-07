/**
 * Webkul Software.
 *
 *
 *
 * @category  Webkul
 * @package   Webkul_Mobikul
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
define([
    "jquery",
    "mage/template",
    "mage/translate",
    "Magento_Ui/js/modal/alert",
    "jquery/ui",
], function ($, mageTemplate, $t, alert) {
    "use strict";
    $.widget("categorysmallbannerimage.setattr", {
        _create: function () {
            var clicked = false;
            var attribute = this.options;
            var count = attribute.key;

            $("#container").on("click", "[data-index=mobikul-configuration]", function () {
                $("[data-index=ar_model_file_ios]").after($("#wk-mobikul-categorysmallbannerimages").html());
                $("#wk-mobikul-categorysmallbannerimages").remove();
                if (clicked == false) {
                    if (parseInt(attribute.arType) == 1) {
                        $("#container [data-index=ar_2d_file]").hide();
                        $(".categorysmallbanner-image").show();
                    } else {
                        $("#container [data-index=ar_model_file_android]").hide();
                        $("#container [data-index=ar_model_file_ios]").hide();
                        $(".categorysmallbanner-image").hide();
                    }
                }
                clicked = true;
            });

            $(document).on("click", "#wk-mobikul-categorysmallbanner-add-more", function () {
                var progressTmpl = mageTemplate("#wk-categorysmallbanner-template"), tmpl;
                tmpl = progressTmpl(
                    {
                        data: {
                            index: count,
                        }
                    }
                );
                $("#wk-mobikul-category-smallbanner").after(tmpl);
                count++;
            });

            $("#container").on("click", ".wk-mobikul-categorysmallbanner-delete", function () {
                $(this).parent().parent().remove();
            });

            $("#container").on("change", "input[name^='mobikul_categoryimages[smallbanner]']", function () {
                var fileName = ($(this).val());
                var extArray = ["png", "jpg", "jpeg"];
                var extension = fileName.substr(fileName.lastIndexOf(".") + 1);
                if (jQuery.inArray(extension, extArray) === -1) {
                    alert({
                        content: $t("Wrong file type given in category banner image.")
                    });
                    $(this).val("");
                    $(this).focus();
                }
            });

            $('.admin__control-file').change(function(){
                $(this).siblings('input').remove();
            });
        }
    });
    return $.categorysmallbannerimage.setattr;
});
