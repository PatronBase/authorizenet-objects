<?php

namespace Academe\AuthorizeNet\Request\Model;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class IntervalTest extends TestCase
{
    public function testSuccess()
    {
        $interval = new Interval(10, Interval::INTERVAL_UNIT_DAYS);
        $this->assertTrue($interval->hasAny());
        $this->assertSame('10', $interval->getLength());
        $this->assertSame('days', $interval->getUnit());

        $interval = new Interval('6', Interval::INTERVAL_UNIT_MONTHS);
        $this->assertSame('6', $interval->getLength());
        $this->assertSame('months', $interval->getUnit());

        $this->assertSame('{"length":"6","unit":"months"}', json_encode($interval));
    }

    public function testInvalidUnit()
    {
        $this->expectException(InvalidArgumentException::class);
        $interval = new Interval(2, "years");
    }

    public function testTooFewDays()
    {
        $this->expectException(OutOfBoundsException::class);
        $interval = new Interval(5, Interval::INTERVAL_UNIT_DAYS);
    }

    public function testTooManyDays()
    {
        $this->expectException(OutOfBoundsException::class);
        $interval = new Interval(500, Interval::INTERVAL_UNIT_DAYS);
    }

    public function testTooFewMonths()
    {
        $this->expectException(OutOfBoundsException::class);
        $interval = new Interval(0, Interval::INTERVAL_UNIT_MONTHS);
    }

    public function testTooManyMonths()
    {
        $this->expectException(OutOfBoundsException::class);
        $interval = new Interval(18, Interval::INTERVAL_UNIT_MONTHS);
    }
}
