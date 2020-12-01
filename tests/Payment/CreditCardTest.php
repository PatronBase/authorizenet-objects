<?php

namespace Academe\AuthorizeNet\Payment;

use PHPUnit\Framework\TestCase;

class CreditCardTest extends TestCase
{
    public function testWithNoCode()
    {
        $card = new CreditCard("4111111111111111", "2020-11");
        $this->assertSame('4111111111111111', $card->getCardNumber());
        $this->assertSame('1111', $card->getLastFourDigits());
        $this->assertSame('2020-11', $card->getExpirationDate());
        $this->assertNull($card->getCardCode());
        $this->assertSame('{"cardNumber":"4111111111111111","expirationDate":"2020-11"}', json_encode($card));
    }

    public function testWithCode()
    {
        $card = new CreditCard("4111111111111112", "2020-12", "999");
        $this->assertSame('4111111111111112', $card->getCardNumber());
        $this->assertSame('1112', $card->getLastFourDigits());
        $this->assertSame('2020-12', $card->getExpirationDate());
        $this->assertSame('999', $card->getCardCode());
        $this->assertSame('{"cardNumber":"4111111111111112","expirationDate":"2020-12","cardCode":"999"}', json_encode($card));
    }
}
