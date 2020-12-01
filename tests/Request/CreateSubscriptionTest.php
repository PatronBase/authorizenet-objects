<?php

namespace Academe\AuthorizeNet\Request;

use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Academe\AuthorizeNet\Amount\Amount;
use Academe\AuthorizeNet\Auth\MerchantAuthentication;
use Academe\AuthorizeNet\Payment\BankAccount;
use Academe\AuthorizeNet\Payment\CreditCard;
use Academe\AuthorizeNet\Payment\OpaqueData;
use Academe\AuthorizeNet\Request\Model\Customer;
use Academe\AuthorizeNet\Request\Model\Interval;
use Academe\AuthorizeNet\Request\Model\NameAddress;
use Academe\AuthorizeNet\Request\Model\Order;
use Academe\AuthorizeNet\Request\Model\PaymentSchedule;
use Academe\AuthorizeNet\Request\Model\Subscription;

class CreateSubscriptionTest extends TestCase
{
    protected $request;

    public function setUp()
    {
        $auth = new MerchantAuthentication("5KP3u95bQpv", "346HZ32z3fP4hTG2");
        $interval = new Interval(1, Interval::INTERVAL_UNIT_MONTHS);
        $paymentSchedule = new PaymentSchedule($interval, "2020-08-30", 12);
        $amount = new Amount('USD', 1027);
        $payment = new OpaqueData("COMMON.ACCEPT.INAPP.PAYMENT", "<long base64 string>");
        $this->subscription = new Subscription($paymentSchedule, $amount, $payment);
        $this->request = new CreateSubscription($auth, $this->subscription);
    }

    /**
     * A minimal request.
     */
    public function testSimple()
    {
        // simple coverage assertions
        $subscription = $this->request->getSubscription();
        $this->assertTrue($subscription->hasAny());
        $this->assertTrue($subscription->getPaymentSchedule()->hasAny());

        $data = [
            "ARBCreateSubscriptionRequest" => [
                "merchantAuthentication" => [
                    "name" => "5KP3u95bQpv",
                    "transactionKey" => "346HZ32z3fP4hTG2",
                ],
                "subscription" => [
                    "paymentSchedule" => [
                        "interval" => [
                            "length" => "1",
                            "unit" => "months",
                        ],
                        "startDate" => "2020-08-30",
                        "totalOccurrences" => "12",
                    ],
                    "amount" => "10.27",
                    "payment" => [
                        "opaqueData" => [
                            "dataDescriptor" => "COMMON.ACCEPT.INAPP.PAYMENT",
                            "dataValue" => "<long base64 string>",
                        ],
                    ],
                ],
            ],
        ];
        $this->assertSame($data, $this->request->toData(true));
        $this->assertSame(
            '{"ARBCreateSubscriptionRequest":{"merchantAuthentication":{"name":"5KP3u95bQpv","transactionKey":"346HZ32z3fP4hTG2"},"subscription":{"paymentSchedule":{"interval":{"length":"1","unit":"months"},"startDate":"2020-08-30","totalOccurrences":"12"},"amount":"10.27","payment":{"opaqueData":{"dataDescriptor":"COMMON.ACCEPT.INAPP.PAYMENT","dataValue":"<long base64 string>"}}}}}',
            json_encode($this->request)
        );
    }

    /**
     * All parameters populated.
     */
    public function testFull()
    {
        $interval = new Interval(30, Interval::INTERVAL_UNIT_DAYS);
        $paymentSchedule = new PaymentSchedule($interval, "2020-09-30", 11, 1);
        $amount = new Amount('USD', "1029");
        $payment = new CreditCard("4111111111111111", "2020-12", "999");
        $trialAmount = new Amount('USD', "0");
        $order = new Order("MERCH1234567890", "Sample merchant description");
        $customer = new Customer(Customer::CUSTOMER_TYPE_INDIVIDUAL, "CUSTOMER123456", "john.smith@example.com");
        $billTo = (new NameAddress("John", "Smith", "Sample Co.", "1 Pike Pl", "Seattle", "WA", "98004", "USA"))
            ->with(["phoneNumber" => "(123) 555-1234", "faxNumber" => "(123) 555-6789"]);
        $shipTo = new NameAddress("Jane", "Doe", "Sample Co. (NZ) Ltd.", "1 Nah Rd", "Christchurch", "", "8001", "NZL");
        $subscription = new Subscription(
            $paymentSchedule,
            $amount,
            $payment,
            "Sample subscription",
            $trialAmount,
            $order,
            $customer,
            $billTo,
            $shipTo
        );

        $request = $this->request->with([
            'refId' => '123456',
            'subscription' => $subscription,
        ]);

        $data = [
            "ARBCreateSubscriptionRequest" => [
                "merchantAuthentication" => [
                    "name" => "5KP3u95bQpv",
                    "transactionKey" => "346HZ32z3fP4hTG2",
                ],
                "refId" => "123456",
                "subscription" => [
                    "name" => "Sample subscription",
                    "paymentSchedule" => [
                        "interval" => [
                            "length" => "30",
                            "unit" => "days",
                        ],
                        "startDate" => "2020-09-30",
                        "totalOccurrences" => "11",
                        "trialOccurrences" => "1",
                    ],
                    "amount" => "10.29",
                    "trialAmount" => "0.00",
                    "payment" => [
                        "creditCard" => [
                            "cardNumber" => "4111111111111111",
                            "expirationDate" => "2020-12",
                            "cardCode" => "999",
                        ],
                    ],
                    "order" => [
                        "invoiceNumber" => "MERCH1234567890",
                        "description" => "Sample merchant description",
                    ],
                    "customer" => [
                        "type" => "individual",
                        "id" => "CUSTOMER123456",
                        "email" => "john.smith@example.com",
                        "phoneNumber" => "(123) 555-1234",
                        "faxNumber" => "(123) 555-6789",
                    ],
                    "billTo" => [
                        "firstName" => "John",
                        "lastName" => "Smith",
                        "company" => "Sample Co.",
                        "address" => "1 Pike Pl",
                        "city" => "Seattle",
                        "state" => "WA",
                        "zip" => "98004",
                        "country" => "USA",
                    ],
                    "shipTo" => [
                        "firstName" => "Jane",
                        "lastName" => "Doe",
                        "company" => "Sample Co. (NZ) Ltd.",
                        "address" => "1 Nah Rd",
                        "city" => "Christchurch",
                        "state" => "",
                        "zip" => "8001",
                        "country" => "NZL",
                    ],
                ],
            ],
        ];

        $this->assertSame($data, $request->toData(true));
    }

    public function testEcheckPayment()
    {
        $payment = (new BankAccount())->with([
            "accountType" => "checking",
            "routingNumber" => "123456789",
            "accountNumber" => "98765432101234567",
            "nameOnAccount" => "J SMITH",
            "echeckType" => "WEB",
        ]);
        $subscription = $this->request->getSubscription()->with(['payment' => $payment]);
        $request = $this->request->with(['subscription' => $subscription]);

        $data = [
            "ARBCreateSubscriptionRequest" => [
                "merchantAuthentication" => [
                    "name" => "5KP3u95bQpv",
                    "transactionKey" => "346HZ32z3fP4hTG2",
                ],
                "subscription" => [
                    "paymentSchedule" => [
                        "interval" => [
                            "length" => "1",
                            "unit" => "months",
                        ],
                        "startDate" => "2020-08-30",
                        "totalOccurrences" => "12",
                    ],
                    "amount" => "10.27",
                    "payment" => [
                        "bankAccount" => [
                            "accountType" => "checking",
                            "routingNumber" => "123456789",
                            "accountNumber" => "98765432101234567",
                            "nameOnAccount" => "J SMITH",
                            "echeckType" => "WEB",
                        ],
                    ],
                ],
            ],
        ];
        $this->assertSame($data, $request->toData(true));
        $this->assertSame(
            '{"ARBCreateSubscriptionRequest":{"merchantAuthentication":{"name":"5KP3u95bQpv","transactionKey":"346HZ32z3fP4hTG2"},"subscription":{"paymentSchedule":{"interval":{"length":"1","unit":"months"},"startDate":"2020-08-30","totalOccurrences":"12"},"amount":"10.27","payment":{"bankAccount":{"accountType":"checking","routingNumber":"123456789","accountNumber":"98765432101234567","nameOnAccount":"J SMITH","echeckType":"WEB"}}}}}',
            json_encode($request)
        );
    }
}
