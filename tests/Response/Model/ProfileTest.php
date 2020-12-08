<?php

namespace Academe\AuthorizeNet\Response\Model;

use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase
{
    public function testSuccess()
    {
        $profile = new Profile([
            'customerProfileId' => '247135',
            'customerPaymentProfileId' => '215458',
            'customerAddressId' => '189691',
        ]);

        $this->assertTrue($profile->hasAny());
        $this->assertSame('247135', $profile->getCustomerProfileId());
        $this->assertSame('215458', $profile->getCustomerPaymentProfileId());
        $this->assertSame('189691', $profile->getCustomerAddressId());

        $this->assertSame(
            '{"customerProfileId":"247135","customerPaymentProfileId":"215458","customerAddressId":"189691"}',
            json_encode($profile)
        );
    }

    public function testEmpty()
    {
        $profile = new Profile([]);
        $this->assertFalse($profile->hasAny());
    }
}
