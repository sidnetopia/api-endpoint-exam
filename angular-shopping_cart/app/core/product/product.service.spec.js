'use strict';

describe('Product', function() {
  var $httpBackend;
  var Product;
  var productsData = [
    {product_name: 'Product X'},
    {product_name: 'Product Y'},
    {product_name: 'Product Z'}
  ];

  // Add a custom equality tester before each test
  beforeEach(function() {
    jasmine.addCustomEqualityTester(angular.equals);
  });

  // Load the module that contains the `Phone` service before each test
  beforeEach(module('core.product'));

  // Instantiate the service and "train" `$httpBackend` before each test
  beforeEach(inject(function(_$httpBackend_, _Product_) {
    $httpBackend = _$httpBackend_;
    $httpBackend.expectGET('http://training.local:8080/product').respond(productsData);

    Product = _Product_;
  }));

  // Verify that there are no outstanding expectations or requests after each test
  afterEach(function () {
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should fetch the products from `http://training.local:8080/product`', function() {
    var products = Product.query();

    expect(products).toEqual([]);

    $httpBackend.flush();
    expect(products).toEqual(productsData);
  });

});
