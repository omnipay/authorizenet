<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class AIMAuthorizeRequestTest extends TestCase
{
    /** @var AIMAuthorizeRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new AIMAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $card = $this->getValidCard();
        $card['email'] = 'example@example.net';
        $this->request->initialize(
            array(
                'clientIp' => '10.0.0.1',
                'amount' => '12.00',
                'customerId' => 'cust-id',
                'card' => $card,
                'duplicateWindow' => 0,
                'solutionId' => 'SOL12345ID',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertEquals('authOnlyTransaction', $data->transactionRequest->transactionType);
        $this->assertEquals('10.0.0.1', $data->transactionRequest->customerIP);
        $this->assertEquals('cust-id', $data->transactionRequest->customer->id);
        $this->assertEquals('example@example.net', $data->transactionRequest->customer->email);
        $this->assertEquals('SOL12345ID', $data->transactionRequest->solution->id);

        // Issue #38 Make sure the transactionRequest properties are correctly ordered.
        // This feels messy, but works.
        $transactionRequestProperties = array_keys(get_object_vars($data->transactionRequest));
        // The names of the properies of the $data->transactionRequest object, in the order in
        // which they must be defined for Authorize.Net to accept the transaction.
        $keys = array(
            "transactionType",
            "amount",
            "payment",
            "solution",
            "order",
            "customer",
            "billTo",
            "shipTo",
            "customerIP",
            "transactionSettings"
        );
        $this->assertEquals($keys, $transactionRequestProperties);

        $setting = $data->transactionRequest->transactionSettings->setting[0];
        $this->assertEquals('testRequest', $setting->settingName);
        $this->assertEquals('false', $setting->settingValue);
        $this->assertObjectNotHasAttribute('trackData', $data->transactionRequest->payment);
        $this->assertObjectNotHasAttribute('retail', $data->transactionRequest);
    }

    public function testGetDataTestMode()
    {
        $this->request->setTestMode(true);

        $data = $this->request->getData();

        $setting = $data->transactionRequest->transactionSettings->setting[0];
        $this->assertEquals('testRequest', $setting->settingName);
        $this->assertEquals('true', $setting->settingValue);
    }

    public function testGetDataOpaqueData()
    {
        $this->request->setOpaqueDataDescriptor('COMMON.ACCEPT.INAPP.PAYMENT');
        $this->request->setOpaqueDataValue('jb2RlIjoiNTB');

        $data = $this->request->getData();

        $this->assertEquals('COMMON.ACCEPT.INAPP.PAYMENT', $data->transactionRequest->payment->opaqueData->dataDescriptor);
        $this->assertEquals('jb2RlIjoiNTB', $data->transactionRequest->payment->opaqueData->dataValue);
    }

    public function testShouldIncludeDuplicateWindowSetting()
    {
        $data = $this->request->getData();
        $setting = $data->transactionRequest->transactionSettings->setting[1];
        $this->assertEquals('duplicateWindow', $setting->settingName);
        $this->assertEquals('0', $setting->settingValue);
    }

    public function testGetDataCardPresentTrack1()
    {
        $card = $this->getValidCard();
        $card['tracks'] = '%B4242424242424242^SMITH/JOHN ^2511126100000000000000444000000?;4242424242424242=25111269999944401?';
        $this->request->initialize(array(
            'amount' => '12.12',
            'card' => $card,
            'deviceType' => 1
        ));

        $data = $this->request->getData();

        $this->assertEquals('12.12', $data->transactionRequest->amount);
        $this->assertEquals(
            '%B4242424242424242^SMITH/JOHN ^2511126100000000000000444000000?',
            $data->transactionRequest->payment->trackData->track1);
        $this->assertObjectNotHasAttribute('creditCard', $data->transactionRequest->payment);
        $this->assertEquals('2', $data->transactionRequest->retail->marketType);
        $this->assertEquals('1', $data->transactionRequest->retail->deviceType);
    }

    public function testGetDataCardPresentTrack2()
    {
        $card = $this->getValidCard();
        $card['tracks'] = ';4242424242424242=25111269999944401?';
        $this->request->initialize(array(
            'amount' => '12.12',
            'card' => $card,
            'deviceType' => 1
        ));

        $data = $this->request->getData();

        $this->assertEquals('12.12', $data->transactionRequest->amount);
        $this->assertEquals(
            ';4242424242424242=25111269999944401?',
            $data->transactionRequest->payment->trackData->track2);
        $this->assertObjectNotHasAttribute('creditCard', $data->transactionRequest->payment);
        $this->assertEquals('2', $data->transactionRequest->retail->marketType);
        $this->assertEquals('1', $data->transactionRequest->retail->deviceType);
    }
}
