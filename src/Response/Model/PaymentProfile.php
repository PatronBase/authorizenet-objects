<?php

namespace Academe\AuthorizeNet\Response\Model;

use Academe\AuthorizeNet\Payment\CreditCard;
use Academe\AuthorizeNet\Payment\OpaqueData;
use Academe\AuthorizeNet\Request\Model\NameAddress;
use Academe\AuthorizeNet\Request\Model\PaymentProfile as RequestPaymentProfile;
use Academe\AuthorizeNet\Response\HasDataTrait;

/**
 * Represenation of a customer payment profile
 */
class PaymentProfile extends RequestPaymentProfile
{
    use HasDataTrait;

    /** @var string */
    protected $customerProfileId;
    /** @var string */
    protected $customerPaymentProfileId;

    public function __construct($data)
    {
        parent::__construct();

        $this->setData($data);

        $this->setCustomerType($this->getDataValue('customerType'));
        $billTo = $this->getDataValue('billTo');
        if ($billTo !== null) {
            $this->setBillTo(new NameAddress(
                $this->getDataValue('billTo.firstName'),
                $this->getDataValue('billTo.lastName'),
                $this->getDataValue('billTo.company'),
                $this->getDataValue('billTo.address'),
                $this->getDataValue('billTo.city'),
                $this->getDataValue('billTo.state'),
                $this->getDataValue('billTo.zip'),
                $this->getDataValue('billTo.country')
            ));
        }
        $payment = $this->getDataValue('payment');
        if ($payment !== null) {
            $payment_type = key($payment);
            if ($payment_type !== null) {
                $payment_type = "Academe\\AuthorizeNet\\Payment\\".ucfirst($payment_type);
                switch ($payment_type) {
                    case CreditCard::class:
                        $payment = new CreditCard(
                            $this->getDataValue('payment.creditCard.cardNumber'),
                            $this->getDataValue('payment.creditCard.expirationDate'),
                            $this->getDataValue('payment.creditCard.cardCode')
                        );
                        break;
                    case OpaqueData::class:
                        $payment = new OpaqueData(
                            $this->getDataValue('payment.opaqueData.dataDescriptor'),
                            $this->getDataValue('payment.opaqueData.dataValue')
                        );
                        break;
                    // @todo other payment types
                }
                $this->setPayment($payment);
            }
        }
        $this->setCustomerProfileId($this->getDataValue('customerProfileId'));
        // @todo have backup on data value using paymentProfileId?
        $this->setPaymentProfileId($this->getDataValue('customerPaymentProfileId'));
    }

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        if ($this->hasCustomerProfileId()) {
            $data['customerProfileId'] = $this->getCustomerProfileId();
        }

        return $data;
    }

    /**
     * @return boolean
     */
    public function hasAny()
    {
        return $this->hasCustomerProfileId()
            || $this->hasCustomerPaymentProfileId();
    }

    /**
     * @param string $value
     * @return void
     */
    protected function setCustomerProfileId($value)
    {
        $this->customerProfileId = $value;
    }
}
