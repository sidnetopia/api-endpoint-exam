'use strict';

angular.
  module('shoppingCartApp').
  config(['$routeProvider',
    function config($routeProvider) {
      $routeProvider.
        when('/product', {
          template: '<product-list></product-list>'
        }).
        when('/product/:productId', {
          template: '<product-detail></product-detail>'
        }).
        otherwise('/product');
    }
  ]);
