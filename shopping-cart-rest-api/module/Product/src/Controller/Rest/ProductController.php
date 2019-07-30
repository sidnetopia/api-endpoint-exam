<?php

namespace Product\Controller\Rest;

use Product\Model\ProductTable;
use Product\Filter\ProductFilter;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class ProductController extends AbstractRestfulController
{
    private $ProductTable;
    private $ProductFilter;
    private $hostname;

    public function __construct(
        ProductTable $ProductTable,
        ProductFilter $ProductFilter,
        $hostname
    )
    {
        $this->ProductTable = $ProductTable;
        $this->ProductFilter = $ProductFilter;
        $this->hostname = $hostname;
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
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: ' . $e->getMessage()));
        }

        $productListArray = [];
        foreach ($ProductList as $key => $value) {
            $productListArray[$key] = get_object_vars($value);
            $productListArray[$key]['product_thumbnail'] = trim($value->product_thumbnail, $this->hostname);
        }

        return new JsonModel($productListArray);
    }

    /**
     * Get product details and return error if failed
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
                return new ApiProblemResponse(new ApiProblem(404, 'Entity not found'));
            }
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: ' . $e->getMessage()));
        }

        $productDetailsArray = get_object_vars($ProductDetails);
        $productDetailsArray['product_image'] = trim($ProductDetails['product_image'], $this->hostname);

        return new JsonModel($productDetailsArray);
    }
}