<?php
namespace Cart\Controller\Rest;

use Application\Controller\CoreController;
use Application\Service\CoreService;
use Cart\Filter\CartItemFilter;
use Cart\Model\CartItemTable;
use Cart\Model\CartTable;
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
    private $CartItemFilter;
    private $CoreService;
    private $Product;

    public function __construct(
        CartTable $CartTable,
        ProductTable $ProductTable,
        CartItemTable $CartItemTable,
        $hostname,
        CartItemFilter $CartItemFilter,
        CoreService $CoreService,
        Product $Product
    )
    {
        $this->CartTable = $CartTable;
        $this->CartItemTable = $CartItemTable;
        $this->ProductTable = $ProductTable;
        $this->hostname  = $hostname;
        $this->CartItemFilter = $CartItemFilter;
        $this->CoreService = $CoreService;
        $this->Product = $Product;
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
                ['cart_id' => $Cart->cart_id], true,['product_thumbnail', 'price', 'product_desc']);

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
            $this->CartItemFilter->setData($data);
            if ($this->CartItemFilter->raiseError())
                return $this->CartItemFilter->raiseError();

            $data = $this->CartItemFilter->getValues();
            $data['cart_id'] = $cartId;
            $productId = $data['product_id'];

            $CartItem = $this->CartItemTable->fetchCartItems(['cart_item_id'], [
                'product_id' => $productId,
                'cart_id' => $cartId,
            ])->current();

            $Product = $this->ProductTable->fetchProducts(['weight', 'price'],
                ['product_id' => $productId])->current();

            $cartItemDetails = $this->CartItemTable
                                ->insertOrUpdateCartItem($CartItem, $data ,$Product->weight, $Product->price);

            $cartItemTotals = $cartItemDetails['cartItemTotals'];

            $this->CartTable->updateCartTotals(
                $cartItemTotals['totalWeight'],
                $cartItemTotals['subTotal'],
                $cartItemTotals['subTotal'],
                $cartId
            );

            $response = $cartItemDetails['response'];
        } catch (\Exception $e) {
            $response = ['code' => 500, 'details' => 'Internal Server Error'];
        }

        return new ApiProblemResponse(new ApiProblem($response['code'], $response['details']));
    }

}