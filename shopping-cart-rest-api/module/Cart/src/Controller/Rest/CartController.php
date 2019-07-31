<?php
namespace Cart\Controller\Rest;

use Application\Controller\CoreController;
use Application\Service\CoreService;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
use Cart\Service\CartItemService;
use Product\Filter\ProductFilter;
use Product\Model\Product;
use Product\Model\ProductTable;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class CartController extends CoreController
{
    private $CartTable;
    private $ProductTable;
    private $CartItemTable;
    private $hostname;
    private $ProductFilter;
    private $CoreService;
    private $Product;
    private $CartItemService;

    public function __construct(
        CartTable $CartTable,
        ProductTable $ProductTable,
        CartItemTable $CartItemTable,
        $hostname,
        ProductFilter $ProductFilter,
        CoreService $CoreService,
        Product $Product,
        CartItemService $CartItemService
    )
    {
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
        $this->ProductTable = $ProductTable;
        $this->hostname = $hostname;
        $this->ProductFilter = $ProductFilter;
        $this->CoreService = $CoreService;
        $this->Product = $Product;
        $this->CartItemService = $CartItemService;
    }

    /**
     * Get cart items and cart details
     * Return error if failed
     *
     * @return mixed|JsonModel|ApiProblemResponse
     */
    public function getList()
    {
        try {
            $Cart = $this->CartTable->fetchCart(
                ['cart_id', 'sub_total', 'shipping_total', 'total_amount'])->current();
            $CartItems = $this->CartItemTable->fetchCartItems(['qty', 'item_price' => 'price'],
                ['cart_id' => $Cart->cart_id], ['product_thumbnail', 'price', 'product_desc']);

        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }

        $cartItemArray = $this->CoreService
            ->transformToArrayWithFunction($CartItems, array($this->Product, 'getImagePath'), $this->hostname);

        return new JsonModel(['cartItems' => $cartItemArray, 'cartDetails' => get_object_vars($Cart)]);
    }

    /**
     * Create or update cart item, then update cart
     * Return error if failed
     *
     * @param mixed $data
     * @return mixed|ApiProblemResponse
     */
    public function create($data)
    {
        try {
            $cartId = $this->CartTable->fetchLatestCartId();
            $productId = $this->ProductFilter->sanitize(['product_id' => $data['product_id']])['product_id'];
            $Product = $this->ProductTable->fetchProducts(['weight', 'price', 'stock_qty'],
                ['product_id' => $productId])->current();

            if (!$Product)
                return new ApiProblemResponse(new ApiProblem(404, 'Not Found'));

            $this->ProductFilter->addQtyValidator($Product->stock_qty);
            $this->ProductFilter->setData($data);
            if ($this->ProductFilter->getErrors())
                return $this->ProductFilter->getErrors();

            $data = $this->ProductFilter->getValues();
            $data['cart_id'] = $cartId;


            $CartItem = $this->CartItemTable->fetchCartItems(['cart_item_id'], [
                'product_id' => $productId,
                'cart_id' => $cartId,
            ])->current();

            $cartItemDetails = $this->CartItemService->insertOrUpdateCartItem(
                $CartItem,
                $Product,
                $this->CartItemTable,
                $data
            );

            $cartItemTotals = $cartItemDetails['cartItemTotals'];
            $subTotal = $cartItemTotals['subTotal'];

            $this->CartTable->updateCartTotals(
                $cartItemTotals['totalWeight'],
                $subTotal,
                $subTotal,
                $cartId
            );

            $response = $cartItemDetails['response'];
        } catch (\Exception $e) {
            $response = ['code' => 500, 'details' => 'Internal Server Error'];
        }

        return new ApiProblemResponse(new ApiProblem($response['code'], $response['details']));
    }
}