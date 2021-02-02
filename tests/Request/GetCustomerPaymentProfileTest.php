<?php

namespace Academe\AuthorizeNet\Request;

use PHPUnit\Framework\TestCase;
use Academe\AuthorizeNet\Auth\MerchantAuthentication;
use Academe\AuthorizeNet\Request\GetCustomerPaymentProfile;

class GetCustomerPaymentProfileTest extends TestCase
{
    protected $request;

    public function setUp()
    {
        $auth = new MerchantAuthentication("5KP3u95bQpv", "346HZ32z3fP4hTG2");
        $this->request = (new GetCustomerPaymentProfile($auth, '10000', '20000'))->with([
            'refId' => '123456',
            'unmaskExpirationDate' => false,
        ]);
    }

    /**
     * A minimal request.
     */
    public function testFetch()
    {
        // simple coverage assertions
        $this->assertSame('10000', $this->request->getCustomerProfileId());
        $this->assertSame('20000',  $this->request->getCustomerPaymentProfileId());

        $data = [
            "getCustomerPaymentProfileRequest" => [
                "merchantAuthentication" => [
                    "name" => "5KP3u95bQpv",
                    "transactionKey" => "346HZ32z3fP4hTG2",
                ],
                "refId" => "123456",
                "customerProfileId" => "10000",
                "customerPaymentProfileId" => "20000",
                "unmaskExpirationDate" => false,
            ],
        ];
        $this->assertSame($data, $this->request->toData(true));
        $this->assertSame(
            '{"getCustomerPaymentProfileRequest":{"merchantAuthentication":{"name":"5KP3u95bQpv","transactionKey":"346HZ32z3fP4hTG2"},"refId":"123456","customerProfileId":"10000","customerPaymentProfileId":"20000","unmaskExpirationDate":false}}',
            json_encode($this->request)
        );
    }
}
