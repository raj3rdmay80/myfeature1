var config = {
	config: {
        mixins: {
            'Magento_Checkout/js/sidebar': {
                'Zigly_Checkout/js/sidebar': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Zigly_Checkout/js/model/checkout-data-resolver': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/template/minicart/item/default.html': 'Zigly_Checkout/template/minicart/item/default.html',
            ajaxQty: 'Zigly_Checkout/js/cartAjaxQtyUpdate'
         
        }
    }
};