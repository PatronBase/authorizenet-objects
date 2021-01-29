<?php

namespace Academe\AuthorizeNet\Request;

use Academe\AuthorizeNet\Auth\MerchantAuthentication;

/**
 * Request to fetch the details of a customer's payment profile
 */
class GetCustomerPaymentProfile extends AbstractRequest
{
    protected $refId;
    protected $customerProfileId;
    protected $customerPaymentProfileId;

    /**
     * @param MerchantAuthentication $merchantAuthentication
     * @param string $customerProfileId The gateway ID of the customer profile we want to fetch.
     * @param string $customerPaymentProfileId The gateway ID of the customer payment profile we want to fetch.
     */
    public function __construct(
        MerchantAuthentication $merchantAuthentication,
        $customerProfileId,
        $customerPaymentProfileId
    ) {
        parent::__construct($merchantAuthentication);

        $this->setCustomerProfileId($customerProfileId);
        $this->setCustomerPaymentProfileId($customerPaymentProfileId);
    }

    public function jsonSerialize()
    {
        $data = [
            $this->getMerchantAuthentication()->getObjectName() => $this->getMerchantAuthentication(),
        ];

        if ($this->hasRefId()) {
            $data['refId'] = $this->getRefId();
        }

        $data['customerProfileId'] = $this->getCustomerProfileId();
        $data['customerPaymentProfileId'] = $this->getCustomerPaymentProfileId();

        return [$this->getObjectName() => $data];
    }

    /**
     * @param string $refId Merchant-assigned reference ID for the request.
     */
    protected function setRefId($value)
    {
        $this->refId = $value;
    }

    /**
     * @param string $transId The Authorize.Net assigned identification number for a customer profile.
     */
    protected function setCustomerProfileId($value)
    {
        $this->customerProfileId = $value;
    }

    /**
     * @param string $transId The Authorize.Net assigned identification number for a customer payment profile.
     */
    protected function setCustomerPaymentProfileId($value)
    {
        $this->customerPaymentProfileId = $value;
    }
}
