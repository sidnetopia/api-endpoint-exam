<?php
namespace Product\Model;

use Zend\Db\TableGateway\TableGateway;

class ProductTable
{
    private $TableGateway;

    public function __construct(TableGateway $TableGateway)
    {
        $this->TableGateway = $TableGateway;
    }

    public function fetchProductList()
    {
        $select = $this->TableGateway->getSql()->select();
        $ProductList = $this->TableGateway->selectWith($select);

        return $ProductList;
    }

    public function fetchProduct($product_id)
    {
        $where = array("product_id" => $product_id);
        $select = $this->TableGateway->getSql()->select()->where($where);
        $Product = $this->TableGateway->selectWith($select)->current();
        return $Product;
    }


    public function updateProduct($product_id, $data)
    {
        $where = (array('product_id' => $product_id));
        $this->TableGateway->update($data, $where);
    }
}