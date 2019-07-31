<?php
namespace Job\Model;

use Zend\Db\TableGateway\TableGateway;

class JobOrderTable
{
    private $TableGateway;

    public function __construct(TableGateway $TableGateway)
    {
        $this->TableGateway = $TableGateway;
    }

    public function insertJobOrder($data)
    {
        $this->TableGateway->insert($data);

        return $this->TableGateway->getLastInsertValue();
    }

    public function fetchJobOrder($columns = null)
    {
        $select = $this->TableGateway->getSql()->select();

        if($columns){
            $select->columns($columns);
        }

        $select->order('job_order_id DESC');
        $Job = $this->TableGateway->selectWith($select);

        return $Job;
    }
}