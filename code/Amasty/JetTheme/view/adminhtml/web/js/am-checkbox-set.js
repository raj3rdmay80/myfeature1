define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/form/element/checkbox-set',
    'mage/translate'
], function ($, ko, _, Radio) {
    'use strict';

    return Radio.extend({
        defaults: {
            buttonConfig: {
                defaultStateTitle: 'Show',
                activeStateTitle: 'Hide',
                isButtonVisible: true
            },
            options: null,
            listens: {
                uploadedImage: 'handleUploadedImage',
                isBlockVisible: 'getButtonTitle',
                value: 'findItemByKey'
            },
            isBlockVisible: true,
            uploadedImage: false,
            selectedItem: null,
            value: null
        },

        /** @inheritDoc  */
        initialize: function () {
            this._super();

            return this;
        },

        /** @inheritDoc */
        initObservable: function () {
            this._super()
                .observe(['options', 'uploadedImage', 'isBlockVisible', 'selectedItem']);

            return this;
        },

        /**
         * Handle button title
         * @returns {String}
         */
        getButtonTitle: function () {
            return this.isBlockVisible()
                ? $.mage.__(this.buttonConfig.activeStateTitle)
                : $.mage.__(this.buttonConfig.defaultStateTitle);
        },

        /**
         * Show/Hide block
         * @returns {void}
         */
        toggleBlockVisibility: function () {
            this.isBlockVisible(!this.isBlockVisible());
        },

        /**
         * Find item by key after selection
         * @returns {Object}
         */
        findItemByKey: function () {
            var data = _.find(this.options(), function (item) {
                return item.value === this.value();
            }.bind(this));

            // eslint-disable-next-line no-unused-expressions
            data ? this.selectedItem(data.label) : this.selectedItem(null);

            return this;
        },

        /**
         * Handle block based on uploaded image
         * @param {Array} image
         * @returns {void}
         */
        handleUploadedImage: function (image) {
            if (_.isArray(image) && image.length) {
                this.clearValue();
                this.isBlockVisible(false);
            } else {
                this.reset();
            }
        },

        /**
         * Clear value to reset field
         * @returns {void}
         */
        clearValue: function () {
            this.value(null);
        }
    });
});
