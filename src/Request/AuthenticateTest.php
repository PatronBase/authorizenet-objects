<?php

namespace Academe\AuthorizeNet\Request;

/**
 * Request to create a recurring billing subscription
 *
 * @see https://developer.authorize.net/api/reference/index.html#gettingstarted-section-section-header
 */
class AuthenticateTest extends AbstractRequest
{
    protected $refId;

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
}
