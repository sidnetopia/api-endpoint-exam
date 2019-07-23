'use strict';

angular.
module('shoppingCartApp').
config(['$routeProvider',
    function config($routeProvider) {
        $routeProvider.
        when('/product/:productId', {
            template: '<product-detail></product-detail>'
        });
    }
]);
