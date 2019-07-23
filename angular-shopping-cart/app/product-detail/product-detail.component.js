'use strict';

// Register `productDetail` component, along with its associated controller and template
angular
    .module('productDetail')
    .component('productDetail', {
        templateUrl: 'product-detail/product-detail.template.html',
        controller: ['$routeParams', 'Product',
            function ProductDetailController($routeParams, Product) {
                var self = this;

                self.productId = $routeParams.productId;
                self.product = Product.get({productId: self.productId}, function (product) {
                    self.price = product.price;
                });

                self.qtyInput = 1;
                self.onQtyInputChange = function () {
                    let price = this.qtyInput * self.product.price;
                    self.price = !isNaN(price) ? price : '';
                };
            }
        ],
    });
