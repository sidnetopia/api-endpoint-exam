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
            $ProductList = $this->ProductTable->fetchProducts();

        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Internal Server Error'));
        };

        $productList = [];
        foreach ($ProductList as $Product) {
            $Product->getImagePath($this->hostname);
            $productList [] = $Product;
        }

        return new JsonModel($productList);
    }

    /**
     * Get product details, return error if failed
     *
     * @param mixed $productId
     * @return ApiProblemResponse|JsonModel
     */
    public function get($productId)
    {
        $productId = $this->ProductFilter->sanitize(['product_id' => $productId])['product_id'];

        try {
            $ProductDetails = $this->ProductTable->fetchProducts(null, ['product_id' => $productId])->current();

            if (!$ProductDetails) {
                $errorResponse = ['code' => 404, 'details' => 'Product not found'];
            }

            $ProductDetails->getImagePath($this->hostname);

        } catch (\Exception $e) {
            $errorResponse = ['code' => 500, 'details' => 'Internal Server Error'];
        }

        if (isset($errorResponse))
            return new ApiProblemResponse(new ApiProblem($errorResponse['code'], $errorResponse['details']));

        return new JsonModel($ProductDetails->getArrayCopy());
    }
}