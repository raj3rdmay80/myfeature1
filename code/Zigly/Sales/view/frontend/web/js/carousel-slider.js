define([
    'jquery',
    'Zigly_Sales/js/owl.carousel'
    ], function ($) {
        'use strict';
        jQuery.noConflict();
        jQuery("#owl-upcoming, #owl-past").owlCarousel({
            nav : true,
            pagination : false,
            margin : 20,
            autoplay : false,
            dots : false,
            items : 2,
            responsive: {
                0:{
                    items: 1,
                    nav: true
                },
                800:{
                    items: 2,
                    nav: true
                }
            }
      });
});