define([
    'jquery',
    'ko',
    'underscore',
    'uiElement',
    'Amasty_JetTheme/js/model/colors-config',
    'jquery/colorpicker/js/colorpicker',
    'domReady!'
], function ($, ko, _, Element, colorsModel) {
    'use strict';

    return Element.extend({
        defaults: {
            ignoreTmpl: {
                templates: false
            },
            inputUiElement: null,
            isInputInitialized: false,
            isNewColorApplied: false,
            fieldId: null,
            listens: {
                value: 'setColor',
                isInputInitialized: 'setColor initializeColorPicker',
                isSetToInitial: 'resetColorToInitial',
                isNewColorApplied: 'toggleIsDefaultValue'
            },
            isSetToInitial: colorsModel.isResetColor,
            htmlId: null
        },

        /** @inheritDoc  */
        initialize: function () {
            this._super();

            colorsModel.colorsConfig.subscribe(function (data) {
                if (data && _.has(data, this.fieldId)) {
                    this.value(data[this.fieldId]);
                    this.isNewColorApplied(true);
                    this.value.valueHasMutated();
                }
            }.bind(this));

            $.async({
                selector: '#' + this.htmlId
            }, function (element) {
                this.inputUiElement = $(element);
                this.cachedValue = this.value();
                this.getIsDefaultValue();
                this.isInputInitialized(true);
            }.bind(this));

            return this;
        },

        /**
         * @returns {void}
         */
        getIsDefaultValue: function () {
            this.useDefaultElement = $('#row_' + this.htmlId).find('#' + this.htmlId + '_inherit');

            if (this.useDefaultElement.length) {
                this.useDefaultValue = this.useDefaultElement.is(':checked');
            }
        },

        /**
         * @returns {void}
         */
        resetColorToInitial: function (data) {
            if (data) {
                this.value(this.cachedValue);
                this.isNewColorApplied(false);
            }
        },

        /**
         * @returns {void}
         */
        toggleIsDefaultValue: function () {
            if (!this.useDefaultElement.length) {
                return;
            }

            if (this.isNewColorApplied() && this.useDefaultElement.is(':checked')) {
                this.useDefaultElement.trigger('click');

                return;
            }

            if (!this.isNewColorApplied() && this.useDefaultElement.is(':checked') !== this.useDefaultValue) {
                this.useDefaultElement.trigger('click');
            }
        },

        /** @inheritDoc */
        initObservable: function () {
            this._super()
                .observe(['value', 'isInputInitialized', 'isNewColorApplied']);

            return this;
        },

        /**
         * @param {String} color
         * @returns {string}
         */
        inverseColor: function (color) {
            return (0xFFFFFF - ('0x' + color)).toString(16)
                .padStart(6, '0')
                .toUpperCase();
        },

        initializeColorPicker: function () {
            _.debounce(function () {
                this.inputUiElement.ColorPicker({
                    color: this.value(),
                    onChange: function (hsb, hex) {
                        this.inputUiElement.css({
                            backgroundColor: '#' + hex,
                            color: '#' + this.inverseColor(hex)
                        }).val('#' + hex);
                    }.bind(this)
                });
            }.bind(this), 300)();
        },

        setColor: function () {
            this.inputUiElement.css({
                backgroundColor: this.value(),
                color: this.inverseHex
            }).val(this.value());
        }
    });
});
