define([
    'Magento_Ui/js/lib/view/utils/async',
    'Amasty_JetTheme/js/view/abstract-switcher-button',
    'Amasty_JetTheme/js/model/colors-config'
], function ($, Button, colorsModel) {
    'use strict';

    return Button.extend({
        defaults: {
            actionConfigKey: 'preset_id',
            listens: {
                isElementInitialized: 'setElementListeners'
            },
            presetsElementSelector: 'amasty_jettheme_color_scheme_color_presets',
            urlToLoad: null
        },
        model: colorsModel,

        initialize: function () {
            this._super();

            $.async({
                selector: '#' + this.presetsElementSelector
            }, function (element) {
                this.presetsElement = $(element);
                this.isElementInitialized(true);
            }.bind(this));

            return this;
        },

        /** @inheritDoc */
        initObservable: function () {
            this._super()
                .observe([ 'isElementInitialized' ]);

            return this;
        }
    });
});
