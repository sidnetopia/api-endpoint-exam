<?php
namespace Job\Model;

use Zend\Db\Sql\Insert;
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
        $insert = $this->TableGateway->getSql()->insert();
        foreach ($CartItems as $CartItem) {
            $relation = array(
                'weight' => $CartItem->weight,
                'qty' => $CartItem->qty,
                'unit_price' => $CartItem->unit_price,
                'price' => $CartItem->price,
                'job_order_id' => $jobOrderId,
                'product_id' => $CartItem->product_id
            );

            $insert->values($relation, Insert::VALUES_MERGE);
        }

        $this->TableGateway->insertWith($insert);
    }
}