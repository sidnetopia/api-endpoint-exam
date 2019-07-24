'use strict';

angular.
module('core.cart').
factory('Cart', ['$resource',
    function($resource) {
        return $resource('http://training.local/cart', {}, {
            query: {
                method: 'GET',
            }
        });
    }
]);
