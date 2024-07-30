var config = {
    config: {
        mixins: {
            'Meticulosity_Checkout/js/view/shipping': {
                'Meticulosity_CollectShipping/js/view/shipping-mixin': true
            }
        }
    },
    map: {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default" : "Meticulosity_CollectShipping/js/shipping-save-processor"
        }
    }
};
