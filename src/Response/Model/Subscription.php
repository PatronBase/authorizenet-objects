<?php

namespace Academe\AuthorizeNet\Response\Model;

use Academe\AuthorizeNet\Response\HasDataTrait;
use Academe\AuthorizeNet\AbstractModel;

/**
 * Single Response subscription reference.
 * This is the bare minimum of information; more available via the subscriptions API.
 */
class Subscription extends AbstractModel
{
    use HasDataTrait;

    protected $id;
    protected $payNum;

    public function __construct($data)
    {
        $this->setData($data);

        $this->setId($this->getDataValue('id'));
        $this->setPayNum($this->getDataValue('payNum'));
    }

    public function jsonSerialize()
    {
        $data = [
            'id' => $this->getId(),
            'payNum' => $this->getPayNum(),
        ];

        return $data;
    }

    protected function setId($value)
    {
        $this->id = $value;
    }

    protected function setPayNum($value)
    {
        $this->payNum = $value;
    }
}
