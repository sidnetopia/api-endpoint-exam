'use strict';

// Register `productList` component, along with its associated controller and template
angular
    .module('productList')
    .component('productList', {
    templateUrl: 'product-list/product-list.template.html',
    controller: ['Product',
        function ProductListController(Product) {
            this.products = Product.query();
        }
    ]
});
