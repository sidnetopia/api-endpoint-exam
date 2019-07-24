'use strict';

angular.
module('shoppingCartApp').
config(['$routeProvider',
    function config($routeProvider) {
        $routeProvider.
        when('/cart', {
            template: '<cart></cart>'
        });
    }
]);
