'use strict';

// Register `payment` component, along with its associated controller and template
angular
    .module('payment')
    .component('payment', {
        templateUrl: 'Module/Payment/payment/payment.template.html',
        controller: ['Cart', 'Job', '$location',
            function PaymentController(Cart, Job, $location) {
                var self = this;

                self.cart = Cart.query(function (cart) {
                    self.cartItems = cart.cartItems;
                    self.cartDetails = cart.cartDetails;
                    if (self.cartDetails.shipping_total <= 0) {
                        alert('Shipping not yet set!');
                        $location.path('/');
                    }
                });

                self.addJobOrder = function() {
                    if (confirm("Please confirm your order.")) {
                        Job.save({}, function (response) {
                            if (response.status >= 200 && response.status < 300) {
                                $location.path('/job');
                            } else {
                                alert(response.detail);
                            }
                        });
                    }
                };
            }
        ]
    });
