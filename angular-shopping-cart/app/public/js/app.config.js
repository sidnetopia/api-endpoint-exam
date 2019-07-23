'use strict';

angular.
  module('shoppingCartApp').
  config(['$routeProvider', '$locationProvider',
    function config($routeProvider, $locationProvider) {
      $routeProvider.
        when('/product', {
          template: '<product-list></product-list>'
        }).
        when('/product/:productId', {
          template: '<product-detail></product-detail>'
        }).
        otherwise('/product');
      $locationProvider.html5Mode(true).hashPrefix('!');
    }
  ]);
