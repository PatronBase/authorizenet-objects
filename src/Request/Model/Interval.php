<?php

namespace Academe\AuthorizeNet\Request\Model;

use Academe\AuthorizeNet\AbstractModel;
use InvalidArgumentException;
use OutOfBoundsException;

/**
 * Representation of a recurring billing time interval (time between payments)
 */
class Interval extends AbstractModel
{
    const INTERVAL_UNIT_DAYS = 'days';
    const INTERVAL_UNIT_MONTHS = 'months';

    protected $length;
    protected $unit;

    public function __construct(
        $length,
        $unit
    ) {
        parent::__construct();

        $this->setUnit($unit);
        $this->setLength($length);
    }

    public function jsonSerialize()
    {
        $data = [];

        $data['length'] = $this->getLength();
        $data['unit'] = $this->getUnit();

        return $data;
    }

    public function hasAny()
    {
        return true;
    }

    /**
     * @param string|int $value
     * @throws OutOfBoundsException  If the length is outside of the range for the current unit
     */
    protected function setLength($value)
    {
        $unit = $this->getUnit();
        $value = (int) $value;
        if ($unit === self::INTERVAL_UNIT_DAYS && ($value < 7 || $value > 365)) {
            throw new OutOfBoundsException('For a unit of days, $value must be between 7 and 365, inclusive');
        }
        if ($unit === self::INTERVAL_UNIT_MONTHS && ($value < 1 || $value > 12)) {
            throw new OutOfBoundsException('For a unit of months, $value must be between 1 and 12, inclusive');
        }

        $this->length = (string) $value;
    }

    /**
     * @param string $value
     * @throws InvalidArgumentException  If not one of the unit constants
     */
    protected function setUnit($value)
    {
        if (! in_array($value, [self::INTERVAL_UNIT_DAYS, self::INTERVAL_UNIT_MONTHS])) {
            throw new InvalidArgumentException('$value must be one of the defined INTERVAL_UNIT_* constants');
        }

        $this->unit = $value;
    }
}
