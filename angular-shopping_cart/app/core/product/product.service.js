'use strict';

angular.
  module('core.product').
  factory('Product', ['$resource',
    function($resource) {
      return $resource('http://training.local/product/:productId', {}, {
        query: {
          method: 'GET',
          isArray: true
        }
      });
    }
  ]);
