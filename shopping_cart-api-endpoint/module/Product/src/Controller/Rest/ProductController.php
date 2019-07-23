<?php
namespace Product\Controller\Rest;

use Product\Model\Product;
use Product\Model\ProductTable;
use Product\Service\ProductService;
use Product\Filter\ProductFilter;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class ProductController extends AbstractRestfulController
{
    private $ProductTable;
    private $Product;
    private $ProductService;
    private $ProductFilter;

    public function __construct(
        ProductTable   $ProductTable,
        Product        $Product,
        ProductService $ProductService,
        ProductFilter  $ProductFilter
    )
    {
        $this->ProductTable   = $ProductTable;
        $this->Product        = $Product;
        $this->ProductService = $ProductService;
        $this->ProductFilter  = $ProductFilter;
    }

    /**
     * Get product list, return error if failed
     *
     * @return ApiProblemResponse|JsonModel
     */
    public function getList()
    {
        try{
            $productList = $this->ProductTable->fetchProductList();
        } catch (\Exception $e){
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: '. $e->getMessage()));
        }

        $productListArray = $productList->toArray();
        foreach($productListArray as $key => $value) {
            $productListArray[$key]['product_thumbnail'] = $this->Product->getImagePath($value['product_thumbnail']);
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
            $productDetails = $this->ProductTable->fetchProduct($productId);
            if (!$productDetails) {
                return new ApiProblemResponse(new ApiProblem(404, 'Entity not found'));
            }
        } catch (\Exception $e) {
            return new ApiProblemResponse(new ApiProblem(500, 'Caught exception: '. $e->getMessage()));
        }

        $phoneDetailsArray = get_object_vars($productDetails);
        $phoneDetailsArray['product_image'] = $this->Product->getImagePath($phoneDetailsArray['product_image']);

        return new JsonModel($phoneDetailsArray);
    }


}