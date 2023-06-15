const bundle = {
    init: function () {
        let maximumSelectionLength = jQuery('.wpft-bundle-select').data('max_selection_length');
        jQuery('.wpft-bundle-select').selectWoo({
            maximumSelectionLength:maximumSelectionLength,
            ajax: {
                url: WPFTFEJS.ajaxURL,
                placeholder: jQuery(this).data('placeholder'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        //action       : $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
                        term: params.term,
                        action: jQuery(this).data('action'),
                        q: params.term, // search term
                        page: params.page,
                        security: WPFTFEJS.bundle_select_nonce,
                        exclude_ids: jQuery(this).data('exclude_ids'),
                        limit: jQuery(this).data('limit'),
                        //exclude_type : $( this ).data( 'exclude_type' ),
                        //include      : $( this ).data( 'include' ),
                        //limit        : $( this ).data( 'limit' ),
                        //display_stock: $( this ).data( 'display_stock' )
                    };
                },
                processResults: function (data) {
                    var terms = [];
                    if (data) {
                        console.log(data);
                        jQuery.each(data, function (id, text) {
                            terms.push({id: id, text: text});
                        });
                    }
                    return {
                        results: terms
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1
        });
    },
    /*toggleHeight: function (element) {
        if (!element.style.height || element.style.height == '0px') {
            element.style.height = element.scrollHeight + 'px'
        } else {
            element.style.height = '0px';
        }
    }*/
}
module.exports = bundle;