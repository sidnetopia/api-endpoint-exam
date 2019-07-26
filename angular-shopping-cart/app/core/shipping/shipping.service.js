'use strict';

angular.
module('core.shipping').
factory('Shipping', ['$resource',
    function($resource) {
        return $resource('http://training.local/shipping', {}, {
            query: {
                method: 'GET'
            },
        });
    }
]);
