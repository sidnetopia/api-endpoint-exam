'use strict';

describe('Cart', function () {
    var $httpBackend;
    var Cart;
    var cartsData = [
        {
            cartItems: [],
            cartDetails: {}
        },
    ];

    // Add a custom equality tester before each test
    beforeEach(function () {
        jasmine.addCustomEqualityTester(angular.equals);
    });

    // Load the module that contains the `Cart` service before each test
    beforeEach(module('core.product'));

    // Instantiate the service and "train" `$httpBackend` before each test
    beforeEach(inject(function (_$httpBackend_, _Cart_) {
        $httpBackend = _$httpBackend_;
        $httpBackend.expectGET('http://training.local/cart').respond(cartsData);

        Cart = _Cart_;
    }));

    // Verify that there are no outstanding expectations or requests after each test
    afterEach(function () {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });

    it('should fetch the products from `http://training.local/cart`', function () {
        var carts = Cart.query();

        expect(carts).toEqual([]);

        $httpBackend.flush();
        expect(carts).toEqual(cartsData);
    });

});
