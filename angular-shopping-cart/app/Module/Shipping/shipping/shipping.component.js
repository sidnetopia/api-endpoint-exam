'use strict';

// Register `shipping` component, along with its associated controller and template
angular
    .module('shipping')
    .component('shipping', {
        templateUrl: 'Module/Shipping/shipping/shipping.template.html',
        controller: ['Shipping', '$location',
            function ShippingController(Shipping, $location) {
                var self = this;

                Shipping.query(function (shipping) {
                    self.shippingPayment = shipping.shippingPayment;
                });

                self.addShippingDetails = function() {
                    var inputData = {
                        shipping_name: self.shipping_name,
                        shipping_address1: self.shipping_address1,
                        shipping_address2: self.shipping_address2,
                        shipping_address3: self.shipping_address3,
                        shipping_city: self.shipping_city,
                        shipping_state: self.shipping_state,
                        shipping_country: self.shipping_country,
                        shipping_mehod: self.shipping_method
                    };

                    if (confirm("Confirm shipping details")) {
                        Shipping.save(inputData, function (response) {
                            if (response.status >= 200 && response.status <= 300) {
                                    $location.path('/payment');
                            } else {
                                alert(response.detail);
                            }
                        });
                    }
                };
            }
        ]
    });
