'use strict';

// Register `productList` component, along with its associated controller and template
angular
    .module('cart')
    .component('cart', {
        templateUrl: 'Module/Cart/cart/cart.template.html',
        controller: ['Cart',
            function ProductListController(Cart) {
            var self = this;
                this.cart = Cart.query(function (cart) {
                    self.cartItems = cart.cartItems;
                });
            }
        ]
    });
