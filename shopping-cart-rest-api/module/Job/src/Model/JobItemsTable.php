<?php
namespace Job\Model;

use Zend\Db\TableGateway\TableGateway;

class JobItemsTable
{
    private $TableGateway;

    public function __construct(TableGateway $TableGateway)
    {
        $this->TableGateway = $TableGateway;
    }

    public function insertJobItem($data)
    {
        $this->TableGateway->insert($data);
    }

    public function fetchJobItems($columns = null, $where = null, $productColumns = array())
    {
        $select = $this->TableGateway->getSql()->select();

        if ($columns) {
            $select->columns($columns);
        }

        if (!empty($productColumns)) {
            $select->join(
                array("p" => "products"),
                "p.product_id = job_items.product_id",
                $productColumns,
                "INNER"
            );
        }

        if ($where) {
            $select->where($where);
        }

        $CartItems = $this->TableGateway->selectWith($select);

        return $CartItems;
    }

    public function insertMultipleCartItemsToJobItems($CartItems, $jobOrderId)
    {
        $query = 'INSERT INTO ' . $this->TableGateway->getTable().
            ' (`job_order_id`, `product_id`, `weight`, `qty`, `unit_price`, `price`) VALUES ';

        $queryVals = array();
        foreach ($CartItems as $CartItem) {
            $CartItem->job_order_id = $jobOrderId;
            $queryVals[] = '(' . implode(',', get_object_vars($CartItem)) . ')';
        }

        $this->TableGateway->getAdapter()->getDriver()->getConnection()
            ->execute($query . implode(',', $queryVals));
    }
}