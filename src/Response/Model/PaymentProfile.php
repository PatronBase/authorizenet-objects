<?php

namespace Academe\AuthorizeNet\Response\Model;

use Academe\AuthorizeNet\Payment\BankAccount;
use Academe\AuthorizeNet\Payment\CreditCard;
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
    /** @var string[] */
    protected $subscriptionIds;

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
                            $this->getDataValue('payment.creditCard.cardCode'),
                            $this->getDataValue('payment.creditCard.cardType'),
                            $this->getDataValue('payment.creditCard.issuerNumber')
                        );
                        break;
                    case BankAccount::class:
                        $payment = (new BankAccount())->with([
                            'accountType' => $this->getDataValue('payment.bankAccount.accountType'),
                            'routingNumber' => $this->getDataValue('payment.bankAccount.routingNumber'),
                            'accountNumber' => $this->getDataValue('payment.bankAccount.accountNumber'),
                            'nameOnAccount' => $this->getDataValue('payment.bankAccount.nameOnAccount'),
                            'echeckType' => $this->getDataValue('payment.bankAccount.echeckType'),
                            'bankName' => $this->getDataValue('payment.bankAccount.bankName'),
                        ]);
                        break;
                }
                $this->setPayment($payment);
            }
        }
        $this->setCustomerProfileId($this->getDataValue('customerProfileId'));
        // @todo have backup on data value using paymentProfileId?
        $this->setPaymentProfileId($this->getDataValue('customerPaymentProfileId'));
        $this->setSubscriptionIds($this->getDataValue('subscriptionIds'));
    }

    public function jsonSerialize()
    {
        $data = [];

        if ($this->hasCustomerProfileId()) {
            $data['customerProfileId'] = $this->getCustomerProfileId();
        }

        if ($this->hasPaymentProfileId()) {
            $data['customerPaymentProfileId'] = $this->getPaymentProfileId();
        }

        if ($this->hasPayment()) {
            $data['payment'] = [
                $this->getPayment()->getObjectName() => $this->getPayment(),
            ];
        }

        if ($this->hasSubscriptionIds()) {
            $data['subscriptionIds'] = $this->getSubscriptionIds();
        }

        if ($this->hasCustomerType()) {
            $data['customerType'] = $this->getCustomerType();
        }

        if ($this->hasBillTo()) {
            $billTo = $this->getBillTo();

            if ($billTo->hasAny()) {
                $data['billTo'] = $billTo;
            }
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

    /**
     * @param string[] $value
     * @return void
     */
    protected function setSubscriptionIds($value)
    {
        $this->subscriptionIds = $value;
    }
}
