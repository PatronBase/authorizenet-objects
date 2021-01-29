<?php

namespace Academe\AuthorizeNet\Response;

/**
 * Generic response class that any response data can be thrown into.
 *
 * TODO: fields:
 * [ ] clientId
 */

use Academe\AuthorizeNet\AbstractModel;
use Academe\AuthorizeNet\Response\Collections\Messages;
use Academe\AuthorizeNet\Response\Model\PaymentProfile;
use Academe\AuthorizeNet\Response\Model\Profile;
use Academe\AuthorizeNet\Response\Model\TransactionResponse;
use Academe\AuthorizeNet\Response\Model\Transaction;

class Response extends AbstractModel
{
    use HasDataTrait;

    /**
     * Top-level response result code values.
     */
    const RESULT_CODE_OK    = 'Ok';
    const RESULT_CODE_ERROR = 'Error';

    /**
     * @var string
     */
    protected $refId;
    /**
     * @var Messages
     */
    protected $messages;
    /**
     * @var Transaction
     */
    protected $transaction;
    /**
     * @var TransactionResponse
     */
    protected $transactionResponse;
    protected $token;

    /**
     * @var string Gateway-assigned identifier for the subscription; numeric string up to 13 digits
     */
    protected $subscriptionId;
    /**
     * @var Profile
     */
    protected $profile;

    /**
     * @var PaymentProfile
     */
    protected $paymentProfile;

    // TODO: for "Decrypt Visa Checkout Data":
    // shippingInfo
    // billingInfo
    // cardInfo
    // paymentDetails
    //
    // TODO: for getUnsettledTransactionListResponse
    // transactions (collection of Transaction models)
    //
    // TODO: for Create a Subscription ----- @todo for Leith
    // subscription (class)
    // status
    //
    // totalNumInResultSet
    // subscriptionDetails (collection of subscriptionDetails)
    //
    // customerProfileId
    // customerPaymentProfileIdList (collection)
    // customerShippingAddressIdList (collection)
    // validationDirectResponseList (collection)
    //
    // ids (collection of customer profile IDs)
    //
    // customerPaymentProfileId
    // validationDirectResponse (string)
    // defaultPaymentProfile (boolean)
    //
    // subscriptionIds (collection)
    // paymentProfiles (collection)
    //
    // directResponse
    //
    // oh, and it goes on, for page after page of copy-paste documentation

    /**
     * The overall response result code.
     * 'Ok' or 'Error'.
     */
    protected $resultCode;

    /**
     * Feed in the raw data structure (array or nested objects).
     */
    public function __construct($data)
    {
        $this->setData($data);

        $this->setRefId($this->getDataValue('refId'));

        // There is one top-level result code, but dropped one
        // level down into the messages.
        $this->setResultCode($this->getDataValue('messages.resultCode'));

        // Messages should always be at the top level.
        if ($messages = $this->getDataValue('messages')) {
            $this->setMessages(new Messages($messages));
        }

        // Response to creating an authorisation (authOnly), purchase (authCapture)
        // or capture (priorAuthCapture).
        if ($transactionResponse = $this->getDataValue('transactionResponse')) {
            $this->setTransactionResponse(new TransactionResponse($transactionResponse));
        }

        if ($transaction = $this->getDataValue('transaction')) {
            $this->setTransaction(new Transaction($transaction));
        }

        // Response to the Hosted Payment Page Request.
        if ($token = $this->getDataValue('token')) {
            $this->setToken($token);
        }

        // Used in recurring billing
        if ($subscriptionId = $this->getDataValue('subscriptionId')) {
            $this->setSubscriptionId($subscriptionId);
        }

        // Used in recurring billing
        if ($profile = $this->getDataValue('profile')) {
            $this->setProfile(new Profile($profile));
        }

        // Used in customer profiles
        if ($paymentProfile = $this->getDataValue('paymentProfile')) {
            $this->setPaymentProfile(new PaymentProfile($paymentProfile));
        }
    }

    /**
     * Note this does not attempt to rebuild the response data in its
     * original form, but instead aims to collect all the data in the
     * class structure for logging.
     */
    public function jsonSerialize()
    {
        $data = [
            'refId' => $this->getRefId(),
            'resultCode' => $this->getResultCode(),
        ];

        if ($messages = $this->getMessages()) {
            $data['messages'] = $messages;
        }

        if ($transactionResponse = $this->getTransactionResponse()) {
            $data['transactionResponse'] = $transactionResponse;
        }

        if ($transaction = $this->getTransaction()) {
            $data['transaction'] = $transaction;
        }

        if ($token = $this->getToken()) {
            $data['token'] = $token;
        }

        if ($subscriptionId = $this->getSubscriptionId()) {
            $data['subscriptionId'] = $subscriptionId;
        }

        if ($profile = $this->getProfile()) {
            $data['profile'] = $profile;
        }

        return $data;
    }

    protected function setRefId($value)
    {
        $this->refId = $value;
    }

    protected function setMessages(Messages $value)
    {
        $this->messages = $value;
    }

    protected function setTransactionResponse(TransactionResponse $value)
    {
        $this->transactionResponse = $value;
    }

    protected function setTransaction(Transaction $value)
    {
        $this->transaction = $value;
    }

    public function setResultCode($value)
    {
        $this->resultCode = $value;
    }

    /**
     * The token identifies a Hosted Page.
     * Will be valid for 15 minutes from creation.
     */
    public function setToken($value)
    {
        $this->token = $value;
    }

    /**
     * @param string $value
     */
    public function setSubscriptionId($value)
    {
        $this->subscriptionId = $value;
    }

    /**
     * @param Profile $value
     */
    public function setProfile(Profile $value)
    {
        $this->profile = $value;
    }

    /**
     * @param PaymentProfile $value
     */
    public function setPaymentProfile(PaymentProfile $value)
    {
        $this->paymentProfile = $value;
    }
}
