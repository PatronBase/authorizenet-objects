<?php

namespace Academe\AuthorizeNet\Response\Model;

use Academe\AuthorizeNet\Payment\BankAccount;
use Academe\AuthorizeNet\Payment\CreditCard;
use PHPUnit\Framework\TestCase;

class PaymentProfileTest extends TestCase
{
    public function testEmpty()
    {
        $profile = new PaymentProfile([]);
        $this->assertFalse($profile->hasAny());
    }
    
    public function testCreditCard()
    {
        $profile = new PaymentProfile([
            "customerProfileId" => "39598611",
            "customerPaymentProfileId" => "35936989",
            "payment" => [
                "creditCard" => [
                    "cardNumber" => "XXXX1111",
                    "expirationDate" => "XXXX",
                    "cardType" => "Visa",
                    "issuerNumber" => "411111",
                    // @todo add support for this flag
                    // "isPaymentToken" => true,
                ],
            ],
            "subscriptionIds" => [
                "3078153",
                "3078154",
            ],
            "customerType" => "individual",
            "billTo" => [
                "firstName" => "John",
                "lastName" => "Smith",
            ],
        ]);
        $this->assertTrue($profile->hasAny());
        $this->assertInstanceOf(CreditCard::class, $profile->getPayment());
        $this->assertSame('individual', $profile->getCustomerType());

        // can't guarantee order of properties, so need to test slightly differently
        $this->assertSame(
            '{"customerProfileId":"39598611","customerPaymentProfileId":"35936989","payment":{"creditCard":'
                .'{"cardNumber":"XXXX1111","expirationDate":"XXXX","cardType":"Visa","issuerNumber":"411111"'
                // .',"isPaymentToken":true'
                .'}},"subscriptionIds":["3078153","3078154"],"customerType":"individual",'
                .'"billTo":{"firstName":"John","lastName":"Smith"}}',
            json_encode($profile)
        );
    }

    public function testBankAccount()
    {
        $profile = new PaymentProfile([
            "customerProfileId" => "39598611",
            "customerPaymentProfileId" => "35936989",
            "payment" => [
                "bankAccount" => [
                    "accountType" => "checking",
                    "routingNumber" => "123456789",
                    "accountNumber" => "12345678901234567",
                    "nameOnAccount" => "J SMITH",
                    "echeckType" => "WEB",
                    "bankName" => "Bank of New Zealand",
                ]
            ],
            "subscriptionIds" => [
                "3078153",
                "3078154",
            ],
            "customerType" => "individual",
            "billTo" => [
                "firstName" => "John",
                "lastName" => "Smith",
            ],
        ]);
        $this->assertTrue($profile->hasAny());
        $this->assertInstanceOf(BankAccount::class, $profile->getPayment());
    }

    // @todo invlaid args tests?
}
