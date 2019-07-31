'use strict';

describe('Cart', function () {
    var $httpBackend;
    var Cart;
    var cartData = [
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
    beforeEach(module('core.cart'));

    // Instantiate the service and "train" `$httpBackend` before each test
    beforeEach(inject(function (_$httpBackend_, _Cart_) {
        $httpBackend = _$httpBackend_;
        $httpBackend.expectGET('http://training.local/cart').respond(cartData);

        Cart = _Cart_;
    }));

    // Verify that there are no outstanding expectations or requests after each test
    afterEach(function () {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });

    it('should fetch the carts from `http://training.local/cart`', function () {
        var cart = Cart.query();

        expect(cart).toEqual([]);

        $httpBackend.flush();
        expect(cart).toEqual(cartData);
    });

});
