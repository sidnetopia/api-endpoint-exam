'use strict';

// AngularJS E2E Testing Guide:
// https://docs.angularjs.org/guide/e2e-testing
describe('ShoppingCart Application', function () {

    it('should redirect `index.html` to `index.html#!/product', function () {
        browser.get('index.html');
        expect(browser.getCurrentUrl()).toContain('index.html#!/product');
    });

    describe('View: Product list', function () {

        beforeEach(function () {
            browser.get('index.html#!/product');
        });

        it('should filter the product list as a user types into the search box', function () {
            var productList = element.all(by.repeater('product in $ctrl.products'));
            var query = element(by.model('$ctrl.query'));

            expect(productList.count()).toBe(3);

            query.sendKeys('rock');
            expect(productList.count()).toBe(1);

            query.clear();

            query.sendKeys('paper');
            expect(productList.count()).toBe(1);

            query.clear();
        });

        it('should render product specific links', function () {
            var query = element(by.model('$ctrl.query'));
            query.sendKeys('rock');

            element.all(by.css('.overlay')).first().click();
            expect(browser.getCurrentUrl()).toContain('#!/product/1');
        });

    });

    describe('View: Product detail', function () {
        beforeEach(function () {
            browser.get('#!/product/1');
        });

        it('should display the `rock` page', function () {
            expect(element(by.binding('$ctrl.product.product_name')).getText()).toBe('Rock');
        });

        it('should display the `rock` image', function () {
            var image = element(by.css('img.product_image'));

            expect(image.getAttribute('src')).toContain('images/rock.jpg');
        });

        it('should display `rock` quantity to be 1', function () {
            var input = element(by.css('input.qtyInput'));

            expect(input.getAttribute('value')).toContain(1);
        });

    });

});
