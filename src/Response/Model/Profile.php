<?php

namespace Academe\AuthorizeNet\Response\Model;

use Academe\AuthorizeNet\AbstractModel;
use Academe\AuthorizeNet\Response\HasDataTrait;

/**
 * Represenation of a customer profile
 *
 * @todo check the various forms returned by the API to make sure all formats are covered
 */
class Profile extends AbstractModel
{
    use HasDataTrait;

    /** @var string */
    protected $customerProfileId;
    /** @var string */
    protected $customerPaymentProfileId;
    /** @var string */
    protected $customerAddressId;

    public function __construct($data)
    {
        parent::__construct();

        $this->setData($data);

        $this->setCustomerProfileId($this->getDataValue('customerProfileId'));
        $this->setCustomerPaymentProfileId($this->getDataValue('customerPaymentProfileId'));
        $this->setCustomerAddressId($this->getDataValue('customerAddressId'));
    }

    public function jsonSerialize()
    {
        $data = [];

        if ($this->hasCustomerProfileId()) {
            $data['customerProfileId'] = $this->getCustomerProfileId();
        }

        if ($this->hasCustomerPaymentProfileId()) {
            $data['customerPaymentProfileId'] = $this->getCustomerPaymentProfileId();
        }

        if ($this->hasCustomerAddressId()) {
            $data['customerAddressId'] = $this->getCustomerAddressId();
        }

        return $data;
    }

    /**
     * @return boolean
     */
    public function hasAny()
    {
        return $this->hasCustomerProfileId()
            || $this->hasCustomerPaymentProfileId()
            || $this->hasCustomerAddressId();
    }

    /**
     * @param string $value
     * @return void
     */
    protected function setCustomerProfileId($value)
    {
        $this->customerProfileId = $value;
    }

    /**
     * @param string $value
     * @return void
     */
    protected function setCustomerPaymentProfileId($value)
    {
        $this->customerPaymentProfileId = $value;
    }

    /**
     * @param string $value
     * @return void
     */
    protected function setCustomerAddressId($value)
    {
        $this->customerAddressId = $value;
    }
}
