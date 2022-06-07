define([
    'jquery',
    'Zigly_Catalog/js/owl.carousel'
    ], function ($) {
        'use strict';
        jQuery.noConflict();
        jQuery("#owl-demo, #owl-exclusive").owlCarousel({
            nav : true,
            pagination : false,
            margin : 20,
            autoplay : false,
            dots : false,
            items : 3,
            responsive:{
                0:{
                    items:1,
                    nav:false
                },
                500:{
                    items:2,
                    nav:false
                },
                1000:{
                    items: 3,
                    nav: false
                }
            }
      });
        jQuery("#top-banner").owlCarousel({
            loop:true,
            margin:10,
            nav:false,
            items: 1,
            autoplay : true,
            autoplayTimeout:3000,
        });
        jQuery("#pets-slider").owlCarousel({
            nav : true,
            pagination : false,
            margin : 20,
            autoplay : false,
            dots : true,
            items : 3,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                500:{
                    items:2,
                    nav:true
                },
                1000:{
                    items: 3,
                    nav: true
                }
            }
        });
        jQuery("#bottom-banner").owlCarousel({
            loop:true,
            margin:10,
            nav:true,
            items: 2,
        });
        jQuery("#slider-athome").owlCarousel({
            loop:true,
            margin:10,
            nav:true,
            items: 1,
        });
        jQuery("#offers-card").owlCarousel({
            nav : false,
            pagination : false,
            autoplay : false,
            dots : false,
            items : 4,
            responsiveClass:true,
            loop: true,
            responsive:{
                0:{
                    items:1.5,
                    nav:false
                },
                767:{
                    items:2.5,
                    nav:false
                },
                1000:{
                    items: 3,
                    nav: false
                }
            }
        });
        jQuery("#shop-by-concern").owlCarousel({
            nav : true,
            items : 5,
            loop: false,
            dots: false,
            responsiveClass:true,
            responsive:{
                0:{
                    items:2,
                    nav:true
                },
                767:{
                    items: 3,
                    nav: true
                },
                991:{
                    items: 4,
                    nav: true
                },
                1200:{
                    items: 5,
                    nav: true
                }
            }
        });
        jQuery("#premium-collections").owlCarousel({
            nav : false,
            pagination : false,
            autoplay : false,
            dots : false,
            margin : 20,
            items : 4,
            responsiveClass:true,
            loop:true,
            responsive:{
                0:{
                    items:1.5,
                    nav:false
                },
                767:{
                    items:2.5,
                    nav:false
                },
                1000:{
                    items: 3,
                    nav: false
                },
                1200: {
                    items: 4,
                    nav: false
                }
            }
        });
        jQuery("#our-experts").owlCarousel({
            nav : false,
            pagination : false,
            margin : 20,
            autoplay : false,
            dots : false,
            items : 3,
            responsiveClass:true,
            loop:true,
            responsive:{
                0:{
                    items:1.5,
                    nav:false
                },
                767:{
                    items:2.5,
                    nav:false
                },
                1000:{
                    items: 3,
                    nav: false
                }
            }
        });
});