'use strict';

angular.
module('shoppingCartApp').
config(['$routeProvider',
    function config($routeProvider) {
        $routeProvider.
        when('/shipping', {
            template: '<shipping></shipping>'
        });
    }
]);
