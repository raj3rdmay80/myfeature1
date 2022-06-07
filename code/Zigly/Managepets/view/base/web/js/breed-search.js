define([
    'jquery',
    'mage/url',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'

], function ($,urlbuild) {
    'use strict';
    $.widget('namespace.widgetname', {
        options: {
            autocomplete: 'off',
            minSearchLength: 0,
        },

        _create: function () {
            this.element.attr('autocomplete', this.options.autocomplete);
            var setelement = this.element;
            var breeddata = this.options.breeddata;
            var typevalue = $('#type').find(":selected").val();
            var searchdata = $.grep(breeddata, function( n, i ) {
              return n.species_id===typevalue;
            });
            $(setelement).autocomplete({
                source: searchdata,
                minLength: 0,
                select: function (event, ui) {
                  $("#breed_id").val(ui.item.breed_id);
                }
            }).focus(function(){
               $(this).data("uiAutocomplete").search($(this).val());
            });
            $.validator.addMethod(
                "breedvalidation",
                function(value, element) {
                    var typevalue = $('#type').find(":selected").val();
                    var breedvalue =  $("#breed_id").val();
                    var checkbreed = $.grep(breeddata, function( n, i ) {
                      return n.species_id === typevalue && n.breed_id == breedvalue;
                    });
                    if(!checkbreed.length){
                         return false;
                     }else{
                        return true;
                     }
                },
                $.mage.__("please select correct breed.")
            );
            $(document).on('change', '#breedname', function() {
                $('#breedvalidation').addClass('')
            });
            $(document).on('change', '#type', function() {
                $("#breed_id").val(0)
                var typevalue = $('#type').find(":selected").val();
                var searchdata = $.grep(breeddata, function( n, i ) {
                  return n.species_id===typevalue;
                });
                $(setelement).autocomplete({
                    source: searchdata,
                    minLength: 0,
                    select: function (event, ui) {
                      $("#breed_id").val(ui.item.breed_id);
                    }
                }).focus(function(){
                   $(this).data("uiAutocomplete").search($(this).val());
                });
            });

        },

        // Private method (begin with underscore)
    });

    return $.namespace.widgetname;
});
