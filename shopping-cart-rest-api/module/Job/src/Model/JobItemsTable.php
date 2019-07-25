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

    public function fetchJobItems($job_order_id, $columns)
    {
        $select = $this->TableGateway->getSql()->select()
            ->columns($columns)->where(array('job_order_id' => $job_order_id));
        $JobItems = $this->TableGateway->selectWith($select);
        return $JobItems;
    }
}