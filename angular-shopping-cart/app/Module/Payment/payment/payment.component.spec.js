// 'use strict';
//
// describe('productList', function () {
//
//     // Load the module that contains the `productList` component before each test
//     beforeEach(module('productList'));
//
//     // Test the controller
//     describe('ProductListController', function () {
//         var $httpBackend, ctrl;
//
//         beforeEach(inject(function ($componentController, _$httpBackend_) {
//             $httpBackend = _$httpBackend_;
//             $httpBackend.expectGET('http://training.local/product')
//                 .respond([{product_name: 'Rock'}, {product_name: 'Paper'}]);
//
//             ctrl = $componentController('productList');
//         }));
//
//         it('should create a `product` property with 2 products fetched with `$http`', function () {
//             jasmine.addCustomEqualityTester(angular.equals);
//
//             expect(ctrl.products).toEqual([]);
//
//             $httpBackend.flush();
//             expect(ctrl.products).toEqual([{product_name: 'Rock'}, {product_name: 'Paper'}]);
//         });
//
//     });
//
// });
