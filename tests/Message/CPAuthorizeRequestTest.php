<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CPAuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CPAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'clientIp' => '10.0.0.1',
                'amount' => '12.00',
                'customerId' => 'cust-id',
                'card' => $this->getValidCard(),
                'cpVersion' => '1.0',
                'marketType' => '2',
                'deviceType' => '8',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('AUTH_ONLY', $data['x_type']);
        $this->assertSame('10.0.0.1', $data['x_customer_ip']);
        $this->assertSame('cust-id', $data['x_cust_id']);
        $this->assertSame('1.0', $data['x_cpversion']);
        $this->assertSame('2', $data['x_market_type']);
        $this->assertSame('8', $data['x_device_type']);
        $this->assertArrayNotHasKey('x_test_request', $data);
    }

    public function testGetDataTestMode()
    {
        $this->request->setTestMode(true);

        $data = $this->request->getData();

        $this->assertSame('TRUE', $data['x_test_request']);
    }
}
