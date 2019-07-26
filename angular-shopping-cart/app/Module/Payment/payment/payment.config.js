'use strict';

angular.
module('shoppingCartApp').
config(['$routeProvider',
    function config($routeProvider) {
        $routeProvider.
        when('/payment', {
            template: '<payment></payment>'
        });
    }
]);
