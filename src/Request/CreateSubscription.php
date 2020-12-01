<?php

namespace Academe\AuthorizeNet\Request;

use Academe\AuthorizeNet\Request\Model\Subscription;
use Academe\AuthorizeNet\Auth\MerchantAuthentication;

/**
 * Request to create a recurring billing subscription
 *
 * @see https://developer.authorize.net/api/reference/index.html#recurring-billing-create-a-subscription
 */
class CreateSubscription extends AbstractRequest
{
    protected $objectName = 'ARBCreateSubscriptionRequest';
    protected $refId;
    protected $subscription;

    public function __construct(
        MerchantAuthentication $merchantAuthentication,
        Subscription $subscription
    ) {
        parent::__construct($merchantAuthentication);

        $this->setSubscription($subscription);
    }

    public function jsonSerialize()
    {
        $data = [];

        // Start with the authentication details.
        $data[$this->getMerchantAuthentication()->getObjectName()] = $this->getMerchantAuthentication();

        // Then the optional merchant site reference ID (will be returned in the response,
        // useful for multithreaded applications).
        if ($this->hasRefId()) {
            $data['refId'] = $this->getRefId();
        }

        // Add the expanded subscription.
        $data[$this->getSubscription()->getObjectName()] = $this->subscription;

        // Wrap it all up in a single element.
        // The JSON structure mimics the XML structure, so all the messages will be
        // in an object with a single property.
        return [
            $this->getObjectName() => $data,
        ];
    }

    /**
     * @param string $value  Up to 20 characters
     */
    protected function setRefId($value)
    {
        $this->refId = $value;
    }

    /**
     * @param Subscription $value  The expanded subscription details
     */
    protected function setSubscription(Subscription $value)
    {
        $this->subscription = $value;
    }
}
