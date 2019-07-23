'use strict';

angular
    .module('shoppingCartApp')
    .config(['$routeProvider',
        function config($routeProvider) {
            $routeProvider.otherwise('/product');
        }
]);
