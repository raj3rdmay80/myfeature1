define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (widget) {
        return wrapper.wrap(widget, function (originalFunction, config, element) {
            originalFunction(config, element);

            /**
             * @param {String} url
             * @param {*} fromPages
             */
            $('.product-info-main .reviews-actions .action').click(function (event) {
                event.preventDefault();
                var regexp = /^.*?(#|$)/,
                    anchor = $(this).attr('href').replace(regexp, '');
                $('.product[role="tablist"] [data-role="content"]').each(function (index) {
                    var linkElement = $(this).find('#' + anchor + ', [data-role="product-review"]');
                    if (linkElement.length > 0) {
                        $('.product[role="tablist"]').tabs('activate', index);
                        animation(linkElement);
                        return;
                    }
                });
            });

            function animation(element) {
                $('html, body').animate({
                    scrollTop: element.offset().top - 50
                }, 300);
            }
        });
    }
});
