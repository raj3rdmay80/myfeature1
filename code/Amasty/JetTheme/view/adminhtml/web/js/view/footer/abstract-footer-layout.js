define([
    'jquery',
    'ko',
    'underscore',
    'uiElement',
    'Amasty_JetTheme/js/model/custom-footer'
], function ($, ko, _, Element, footerModel) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Amasty_JetTheme/footer/abstract-footer-layout',
            layoutBlocks: [],
            cachedLayoutBlocks: '',
            isVisible: false,
            selectedLayout: [],
            updatedLayoutConfig: {},
            layoutSelectors: {
                columnWrapElement: '.amtheme-footer-container',
                columnElement: '.column',
                itemWrapElement: '.item-block',
                itemElement: '.item-block'
            }
        },

        /** @inheritDoc */
        initObservable: function () {
            this._super()
                .observe(['isVisible', 'layoutBlocks', 'selectedLayout']);

            footerModel.activeLayout.subscribe(function (layoutCode) {
                if (layoutCode) {
                    this.isVisible(layoutCode === this.index);
                }
            }.bind(this));

            footerModel.fieldValue.subscribe(function () {
                this.setSelectedLayout();
            }.bind(this));

            return this;
        },

        /**
         * Toggle layout visibility
         * @returns {void}
         */
        toggleLayoutVisibility: function () {
            footerModel.setActiveLayout(this.index);
        },

        /**
         * Update field value
         * @param {Object} data
         * @param {Number} index
         * @returns {void}
         */
        setUpdatedLayoutConfig: function (data, index) {
            footerModel.fieldValue()[this.index][index] = data;
            footerModel.setFieldValue(footerModel.fieldValue());
        },

        /**
         * Set layout data based on footer model config
         * @returns {void}
         */
        setSelectedLayout: function () {
            if (this.cachedLayoutBlocks === JSON.stringify(footerModel.fieldValue()[this.index])) {
                return;
            }

            this.layoutBlocks([]);
            _.each(footerModel.fieldValue()[this.index], function (array, index) {
                this.layoutBlocks()[index] = [];
                _.each(array, function (name) {
                    this.layoutBlocks()[index].push(footerModel.footerBlocks()[name]);
                }.bind(this));
            }.bind(this));

            this.cachedLayoutBlocks = JSON.stringify(footerModel.fieldValue()[this.index]);
            this.layoutBlocks.valueHasMutated();
        },

        /**
         * Init jQuery sortable widget
         * @param {Element} element
         * @returns {void}
         */
        afterColumnRender: function (element) {
            var self = this;

            $(element).sortable({
                connectWith: [ this.layoutSelectors.columnElement ],
                placeholder: 'place',
                items: this.layoutSelectors.itemElement,
                start: function (event, ui) {
                    ui.item.css('opacity', 0.6);
                },
                stop: function (event, ui) {
                    ui.item.css('opacity', 1);
                },
                update: function () {
                    var $this = $(this),
                        newDataObject = [];

                    _.each($this.find('.item'), function (item) {
                        var value = $(item).data('value');

                        if (value) {
                            newDataObject.push(value);
                        }
                    });

                    self.setUpdatedLayoutConfig(newDataObject, $this.data('index'));
                }
            });
        }
    });
});
