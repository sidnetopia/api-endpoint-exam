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

    public function fetchProducts($columns = null, $where = null)
    {
        $select = $this->TableGateway->getSql()->select();
        if ($columns)
            $select->columns($columns);
        if ($where)
            $select->where($where);

        $Product = $this->TableGateway->selectWith($select);

        return $Product;
    }


    public function updateProduct($product_id, $data)
    {
        $where = (array('product_id' => $product_id));
        $this->TableGateway->update($data, $where);
    }
}