define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/components/button',
    'Amasty_JetTheme/js/action/get-config'
], function ($, _, Button, getConfigAction) {
    'use strict';

    return Button.extend({
        defaults: {
            actionConfigKey: '',
            actions: [
                {
                    actionName: 'getConfig',
                    // eslint-disable-next-line no-template-curly-in-string
                    targetName: '${ $.name}'
                }
            ],
            childError: null,
            defaultOptionValue: 'default',
            elementTmpl: 'Amasty_JetTheme/form/get-config-button',
            isErrorPresent: false,
            listens: {
                errorMessage: 'showMessage'
            },
            presetsElement: null,
            presetsElementSelector: null,
            preselectConfigId: null,
            title: 'Apply',
            urlToLoad: null
        },

        /** @inheritDoc */
        initObservable: function () {
            this._super()
                .observe(['errorMessage', 'isErrorPresent']);

            this.errorMessage = this.model.errorMessage;

            return this;
        },

        /**
         * @param {Element} element
         * @returns {Object}
         */
        afterRenderHandler: function (element) {
            this.element = element;

            this.moveButtonToTheEnd();

            return this;
        },

        /**
         * @returns {void}
         */
        moveButtonToTheEnd: function () {
            var $element = $(this.element),
                rowElement = $element.parents('td.value');

            rowElement.append($element.parent());
        },

        /**
         * Set listener
         * @returns {void}
         */
        setElementListeners: function () {
            if (this.presetsElement.length) {
                this.preselectedConfigId = this.presetsElement.val();

                this.presetsElement.on('change', function (event) {
                    this.preselectedConfigId = event.target.value;
                }.bind(this));

                this.getIsDefaultValue();
            }
        },

        getIsDefaultValue: function () {
            this.useDefaultElement = $('#row_' + this.presetsElementSelector)
                .find('#' + this.presetsElementSelector + '_inherit');

            if (this.useDefaultElement.length) {
                this.disabled(this.useDefaultElement.is(':checked'));
            }
        },

        /**
         * @returns {void}
         */
        showMessage: function () {
            this.isErrorPresent(!!this.errorMessage());
        },

        /**
         * Call Ajax from action to get config
         * @returns {void}
         */
        getConfig: function () {
            var configId = this.preselectedConfigId ? this.preselectedConfigId : this.defaultOptionValue,
                actionData = _.object([this.actionConfigKey], [configId]);

            this._resetErrors();

            if (this.urlToLoad) {
                getConfigAction.getConfig(this.urlToLoad, actionData, this.model);
            }
        },

        /**
         * @private
         * @returns {void}
         */
        _resetErrors: function () {
            this.isErrorPresent(false);
            this.errorMessage('');
        }
    });
});
