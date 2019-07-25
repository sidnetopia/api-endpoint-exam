'use strict';

// Register `productDetail` component, along with its associated controller and template
angular
    .module('productDetail')
    .component('productDetail', {
        templateUrl: 'Module/Product/product-detail/product-detail.template.html',
        controller: ['$routeParams', 'Product', 'Cart',
            function ProductDetailController($routeParams, Product, Cart) {
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
                self.addCartItem = function() {
                    var data = {
                        qty: self.qtyInput,
                        product_id: self.productId
                    };
                    // console.log(data);
                    Cart.save();
                    // response.success(function(data, status, headers, config) {
                    //     alert(JSON.stringify({data: data}));
                    // });
                    // response.error(function(data, status, headers, config) {
                    //     alert( "failure message: " + JSON.stringify({data: data}));
                    // });
                };
            }
        ],
    });
