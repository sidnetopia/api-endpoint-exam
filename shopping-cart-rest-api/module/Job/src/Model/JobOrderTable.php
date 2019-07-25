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
}