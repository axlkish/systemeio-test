<?php

namespace Tests;

use App\PaymentProcessor\PaypalPaymentProcessor;
use App\PaymentProcessor\StripePaymentProcessor;
use PHPUnit\Framework\TestCase;
use App\Controller\PaymentAdapter;

class PaymentAdapterTest extends TestCase
{
    public function testPaypalSuccess(): void
    {
        // create mocks
        [$paypalMock, $stripeMock] = $this->createMocks();

        // get payment adapter
        $paymentAdapter = $this->getPaymentAdapter($paypalMock, $stripeMock);

        // test
        $result = $paymentAdapter->paypal(100);
        $this->assertTrue($result);
    }

    public function testPaypalError(): void
    {
        // create mocks
        [$paypalMock, $stripeMock] = $this->createMocks();

        // configure paypal mock
        $paypalMock
            ->expects($this->once())
            ->method('pay')
            ->willThrowException(new \Exception('Too high price'));

        // get payment adapter
        $paymentAdapter = $this->getPaymentAdapter($paypalMock, $stripeMock);

        // test
        $result = $paymentAdapter->paypal(200);
        $this->assertEquals('Too high price', $result);
    }

    public function testStripeSuccess(): void
    {
        // create mocks
        [$paypalMock, $stripeMock] = $this->createMocks();

        // configure stripe mock
        $stripeMock
            ->expects($this->once())
            ->method('processPayment')
            ->willReturn(true);

        // get payment adapter
        $paymentAdapter = $this->getPaymentAdapter($paypalMock, $stripeMock);

        // test
        $result = $paymentAdapter->stripe(100);
        $this->assertTrue($result);
    }

    public function testStripeError(): void
    {
        // create mocks
        [$paypalMock, $stripeMock] = $this->createMocks();

        // configure stripe mock
        $stripeMock
            ->expects($this->once())
            ->method('processPayment')
            ->willReturn(false);

        // get payment adapter
        $paymentAdapter = $this->getPaymentAdapter($paypalMock, $stripeMock);

        // test
        $result = $paymentAdapter->stripe(5);
        $this->assertEquals('price less than 10', $result);
    }

    private function createMocks()
    {
        return [$this->createPaypalMock(), $this->createStripeMock()];
    }

    private function getPaymentAdapter(
        PaypalPaymentProcessor $paypal,
        StripePaymentProcessor $stripe
    )
    {
        return new PaymentAdapter($paypal, $stripe);
    }

    private function createPaypalMock()
    {
        return
            $this->getMockBuilder(PaypalPaymentProcessor::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['pay'])
                ->getMock();
    }

    private function createStripeMock()
    {
        return
            $this->getMockBuilder(StripePaymentProcessor::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['processPayment'])
                ->getMock();
    }
}
