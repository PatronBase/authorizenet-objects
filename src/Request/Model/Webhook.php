<?php

namespace Academe\AuthorizeNet\Request\Model;

use Academe\AuthorizeNet\AbstractModel;

/**
 * Representation of a webhook
 */
class Webhook extends AbstractModel
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $webhookId;
    protected $name;
    protected $url;
    protected $eventTypes;
    protected $status;

    public function __construct($url = null, $eventTypes = null)
    {
        parent::__construct();

        $this->setUrl($url);
        $this->setEventTypes($eventTypes);
    }

    public function hasAny()
    {
        return $this->hasWebhookId()
            || $this->hasName()
            || $this->hasUrl()
            || $this->hasEventTypes()
            || $this->hasStatus();
    }

    public function jsonSerialize()
    {
        $data = [];

        if ($this->hasWebhookId()) {
            $data['webhookId'] = $this->getWebhookId();
        }

        if ($this->hasName()) {
            $data['name'] = $this->getName();
        }

        if ($this->hasUrl()) {
            $data['url'] = $this->getUrl();
        }

        if ($this->hasEventTypes()) {
            $data['eventTypes'] = $this->getEventTypes();
        }

        if ($this->hasStatus()) {
            $data['status'] = $this->getStatus();
        }

        return $data;
    }

    /**
     * @param string $value
     */
    protected function setName($value)
    {
        $this->name = $value;
    }

    /**
     * @param string $value
     */
    protected function setUrl($value)
    {
        $this->url = $value;
    }

    /**
     * @param string[] $value
     */
    protected function setEventTypes($value)
    {
        $this->eventTypes = $value;
    }

    /**
     * @param string $value
     */
    protected function setStatus($value)
    {
        $this->status = $value;
    }
}
