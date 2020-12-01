<?php

namespace Academe\AuthorizeNet\Request\Model;

use Academe\AuthorizeNet\AbstractModel;
use Academe\AuthorizeNet\AmountInterface;
use Academe\AuthorizeNet\PaymentInterface;

/**
 * Representation of a recurring billing subscription
 */
class Subscription extends AbstractModel
{
    protected $name;
    protected $paymentSchedule;
    protected $amount;
    protected $trialAmount;
    protected $payment;
    protected $order;
    protected $customer;
    protected $billTo;
    protected $shipTo;

    public function __construct(
        PaymentSchedule $paymentSchedule,
        AmountInterface $amount,
        // not required according to the docs?
        PaymentInterface $payment,
        $name = null,
        AmountInterface $trialAmount = null,
        Order $order = null,
        Customer $customer = null,
        NameAddress $billTo = null,
        NameAddress $shipTo = null
    ) {
        parent::__construct();

        $this->setName($name);
        $this->setPaymentSchedule($paymentSchedule);
        $this->setAmount($amount);
        $this->setTrialAmount($trialAmount);
        $this->setPayment($payment);
        $this->setOrder($order);
        $this->setCustomer($customer);
        $this->setBillTo($billTo);
        $this->setShipTo($shipTo);
    }

    public function jsonSerialize()
    {
        $data = [];

        if ($this->hasName()) {
            $data['name'] = $this->getName();
        }

        $data['paymentSchedule'] = $this->getPaymentSchedule();
        $data['amount'] = $this->getAmount();

        if ($this->hasTrialAmount()) {
            $data['trialAmount'] = $this->getTrialAmount();
        }

        $data['payment'] = [$this->getPayment()->getObjectName() => $this->getPayment()];

        if ($this->hasOrder()) {
            $data['order'] = $this->getOrder();
        }

        if ($this->hasCustomer()) {
            $data['customer'] = $this->getCustomer();
            if ($this->hasBillTo()) {
                $billTo = $this->getBillTo();
                if (! $data['customer']->hasPhoneNumber() && $billTo->hasPhoneNumber()) {
                    $data['customer'] = $data['customer']->with(['phoneNumber' => $billTo->getPhoneNumber()]);
                }
                if (! $data['customer']->hasFaxNumber() && $billTo->hasFaxNumber()) {
                    $data['customer'] = $data['customer']->with(['faxNumber' => $billTo->getFaxNumber()]);
                }
            }
        }

        if ($this->hasBillTo()) {
            // @todo would be nice to have without()
            $data['billTo'] = $this->getBillTo()->with(['phoneNumber' => null, 'faxNumber' => null]);
        }

        if ($this->hasShipTo()) {
            // @todo would be nice to have without()
            $data['shipTo'] = $this->getShipTo()->with(['phoneNumber' => null, 'faxNumber' => null]);
        }

        return $data;
    }

    public function hasAny()
    {
        return true;
    }

    /**
     * @param string $value
     */
    protected function setName($value)
    {
        $this->name = $value;
    }

    /**
     * @param PaymentSchedule $value
     */
    protected function setPaymentSchedule(PaymentSchedule $value)
    {
        $this->paymentSchedule = $value;
    }

    /**
     * @param AmountInterface $value
     */
    protected function setAmount(AmountInterface $value)
    {
        $this->amount = $value;
    }

    /**
     * @param AmountInterface $value
     */
    protected function setTrialAmount(AmountInterface $value = null)
    {
        $this->trialAmount = $value;
    }

    /**
     * @param PaymentInterface $value
     */
    protected function setPayment(PaymentInterface $value)
    {
        $this->payment = $value;
    }

    /**
     * @param Order $value
     */
    protected function setOrder(Order $value = null)
    {
        $this->order = $value;
    }

    /**
     * @param Customer $value
     */
    protected function setCustomer(Customer $value = null)
    {
        $this->customer = $value;
    }

    /**
     * @param NameAddress $value
     */
    protected function setBillTo(NameAddress $value = null)
    {
        $this->billTo = $value;
    }

    /**
     * @param NameAddress $value
     */
    protected function setShipTo(NameAddress $value = null)
    {
        $this->shipTo = $value;
    }
}
