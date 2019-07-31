'use strict';

// Register `productDetail` component, along with its associated controller and template
angular
    .module('productDetail')
    .component('productDetail', {
        templateUrl: 'Module/Product/product-detail/product-detail.template.html',
        controller: ['$routeParams', '$location', 'Product', 'Cart',
            function ProductDetailController($routeParams, $location, Product, Cart) {
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

                self.addCartItem = function () {
                    var inputData = {
                        qty: self.qtyInput,
                        product_id: self.productId
                    };

                    Cart.save(inputData, function (response) {
                        if (response.status >= 200 && response.status <= 300) {
                            if (confirm(response.detail + "\nWould you like to view your cart?")) {
                                $location.path('/cart');
                            }
                        } else {
                            alert(response.detail);
                        }
                    });
                };
            }
        ],
    });
