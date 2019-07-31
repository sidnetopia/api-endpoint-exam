<?php
namespace Product\Controller\Rest;

use Application\Controller\CoreController;
use Application\Service\CoreService;
use Product\Model\Product;
use Product\Model\ProductTable;
use Product\Filter\ProductFilter;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class ProductController extends CoreController
{
    private $ProductTable;
    private $ProductFilter;
    private $hostname;
    private $Product;
    private $CoreService;

    public function __construct(
        ProductTable $ProductTable,
        ProductFilter $ProductFilter,
        $hostname,
        Product $Product,
        CoreService $CoreService
    )
    {
        $this->ProductTable = $ProductTable;
        $this->ProductFilter = $ProductFilter;
        $this->hostname = $hostname;
        $this->Product = $Product;
        $this->CoreService = $CoreService;
    }

    /**
     * Get product list, return error if failed
     *
     * @return ApiProblemResponse|JsonModel
     */
    public function getList()
    {
        try {
            $ProductList = $this->ProductTable->fetchProducts([
                'product_id',
                'product_thumbnail',
                'product_name',
                'product_desc',
                'price'
            ]);
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        }

        $productListArray = $this->CoreService
            ->transformToArrayWithFunction($ProductList, array($this->Product, 'getImagePath'), $this->hostname);

        return new JsonModel($productListArray);
    }

    /**
     * Get product details, return error if failed
     *
     * @param mixed $productId
     * @return ApiProblemResponse|JsonModel
     */
    public function get($productId)
    {
        $productId = $this->ProductFilter->sanitize(['productId' => $productId])['productId'];

        try {
            $ProductDetails = $this->ProductTable->fetchProducts([
                'product_image',
                'product_name',
                'product_desc',
                'stock_qty',
                'price'
            ], ['product_id' => $productId])->current();

            if (!$ProductDetails) {
                $response = ['code' => 404, 'details' => 'Entity not found'];
            }
        } catch (\Exception $e) {
            $response = ['code' => 500, 'details' => 'Internal Server Error'];
        }

        if (isset($response))
            return new ApiProblemResponse(new ApiProblem($response['code'], $response['details']));

        $productDetailsArray = get_object_vars($ProductDetails);
        $productDetailsArray = $this->Product->getImagePath($productDetailsArray, $this->hostname);

        return new JsonModel($productDetailsArray);
    }
}