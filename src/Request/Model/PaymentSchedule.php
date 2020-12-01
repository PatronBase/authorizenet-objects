<?php

namespace Academe\AuthorizeNet\Request\Model;

use Academe\AuthorizeNet\AbstractModel;

/**
 * Representation of a recurring billing payment schedule
 */
class PaymentSchedule extends AbstractModel
{
    protected $interval;
    protected $startDate;
    protected $totalOccurrences;
    protected $trialOccurrences;

    public function __construct(
        Interval $interval,
        $startDate,
        $totalOccurrences,
        $trialOccurrences = null
    ) {
        parent::__construct();

        $this->setInterval($interval);
        $this->setStartDate($startDate);
        $this->setTotalOccurrences($totalOccurrences);
        $this->setTrialOccurrences($trialOccurrences);
    }

    public function jsonSerialize()
    {
        $data = [];

        $data['interval'] = $this->getInterval();
        $data['startDate'] = $this->getStartDate();
        $data['totalOccurrences'] = $this->getTotalOccurrences();

        if ($this->hasTrialOccurrences()) {
            $data['trialOccurrences'] = $this->getTrialOccurrences();
        }

        return $data;
    }

    public function hasAny()
    {
        return true;
    }

    /**
     * @param Interval $value
     */
    protected function setInterval(Interval $value)
    {
        $this->interval = $value;
    }

    /**
     * @param string $value  YYYY-MM-DD
     */
    protected function setStartDate($value)
    {
        $this->startDate = $value;
    }

    /**
     * @param string|int $value
     */
    protected function setTotalOccurrences($value)
    {
        $this->totalOccurrences = (string) $value;
    }

    /**
     * @param string|int $value
     */
    protected function setTrialOccurrences($value)
    {
        $this->trialOccurrences = $value === null ? null : (string) $value;
    }
}
