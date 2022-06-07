/**
 *   Slick Slider functionality
 */

define([
    'jquery',
    'underscore',
    'matchMedia',
    'Amasty_Base/vendor/slick/slick.min'
], function ($, _, mediaCheck) {
    'use strict';

    $.widget('am.slickSlider', {
        options: {
            mediaBreakpoint: '(min-width: 768px)',
            sliderClass: 'amtheme-slick-slider',
            dataAttr: 'data-amtheme-js',
            updateOnResize: false,
            updateEvent: 'nav.updated',
            updateClass: 'update-in-progress',
            slickUpdated: false,
            isOnMobile: true,
            responsiveSettings: {},
            sliderOptions: {},
            delay: 500
        },

        /**
         * @private
         * @returns {void}
         */
        _create: function () {
            this.body = $('body');

            if (this.options.isOnMobile) {
                this.options.sliderOptions = $.extend(this.options.sliderOptions, this.options.responsiveSettings);
                this._initSlider();
            } else {
                this._initResponsive();
            }
        },

        /**
         * @private
         * @returns {void}
         */
        _destroySlider: function () {
            if (this.element.hasClass('slick-initialized')) {
                this.element.slick('unslick');
            }
            this.body.off(this.options.updateEvent);
            this.element.removeClass(this.options.sliderClass);
        },

        /**
         * @private
         * @returns {void}
         */
        _initSlider: function () {
            this.element.slick(this.options.sliderOptions);
            this.element.addClass(this.options.sliderClass).attr(this.options.dataAttr, 'slider');
            if (this.options.updateOnResize) {
                this._initOnResizeUpdate();
            }
        },

        /**
         * if update for slick slider is enabled it will be refreshed on 'updateEvent'
         *
         * @private
         * @returns {void}
         */
        _initOnResizeUpdate: function () {
            var self = this;

            this.body.on(this.options.updateEvent, function () {
                self._updateTransitionProcess();
                _.delay(function () {
                    self.element[0].slick.refresh();
                    self._updateTransitionProcess();
                }, self.options.delay);
            });
        },

        /**
         * @private
         * @returns {am.slickSlider}
         */
        _updateTransitionProcess: function () {
            // eslint-disable-next-line no-unused-expressions
            this.options.slickUpdated
                ? this.element.css('opacity', 1).removeClass(this.options.updateClass)
                : this.element.css('opacity', 0.4).addClass(this.options.updateClass);
            this.options.slickUpdated = !this.options.slickUpdated;
            return this;
        },

        /**
         * @private
         * @returns {void}
         */
        _initResponsive: function () {
            var self = this;

            mediaCheck({
                media: self.options.mediaBreakpoint,
                entry: function () {
                    self._initSlider();
                },
                exit: function () {
                    self._destroySlider();
                }
            });
        }
    });

    return $.am.slickSlider;
});
