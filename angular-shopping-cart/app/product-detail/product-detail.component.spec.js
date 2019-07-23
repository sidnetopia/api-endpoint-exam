'use strict';

describe('productDetail', function() {

  // Load the module that contains the `productDetail` component before each test
  beforeEach(module('productDetail'));

  // Test the controller
  describe('ProductDetailController', function() {
    var $httpBackend, ctrl;
    var xyzProductData = {
      product_name: 'product xyz',
      product_image: ['images/url1.jpg']
    };

    beforeEach(inject(function($componentController, _$httpBackend_, $routeParams) {
      $httpBackend = _$httpBackend_;
      $httpBackend.expectGET('http://training.local/product/4').respond(xyzProductData);

      $routeParams.productId = '4';

      ctrl = $componentController('productDetail');
    }));

    it('should fetch the product details', function() {
      jasmine.addCustomEqualityTester(angular.equals);

      expect(ctrl.product).toEqual({});

      $httpBackend.flush();
      expect(ctrl.product).toEqual(xyzProductData);
    });

  });

});
