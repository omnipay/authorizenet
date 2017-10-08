<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest;
use Omnipay\AuthorizeNet\Message\AIMCaptureRequest;
use Omnipay\AuthorizeNet\Message\AIMPurchaseRequest;
use Omnipay\AuthorizeNet\Message\AIMRefundRequest;
use Omnipay\AuthorizeNet\Message\AIMVoidRequest;
use Omnipay\Common\AbstractGateway;

/**
 * Authorize.Net AIM Class
 */
class AIMGateway extends AbstractGateway
{
    /**
     * The device type collecting credit card data.
     */
    const DEVICE_TYPE_UNKNOWN = 1;
    const DEVICE_TYPE_UNATTENDED_TERMINAL = 2;
    const DEVICE_TYPE_SELF_SERVICE_TERMINAL = 3;
    const DEVICE_TYPE_ELECTRONIC_CASH_REGISTER = 4;
    const DEVICE_TYPE_PC_TERMINAL = 5;
    const DEVICE_TYPE_AIRPAY = 6;
    const DEVICE_TYPE_WIRELESS_POS = 7;
    const DEVICE_TYPE_WEBSITE = 8;
    const DEVICE_TYPE_DIAL_TERMINAL = 9;
    const DEVICE_TYPE_VIRTUAL_TERMINAL = 10;

    public function getName()
    {
        return 'Authorize.Net AIM';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiLoginId'        => '',
            'transactionKey'    => '',
            'testMode'          => false,
            'developerMode'     => false,
            'hashSecret'        => '',
            'liveEndpoint'      => 'https://api2.authorize.net/xml/v1/request.api',
            'developerEndpoint' => 'https://apitest.authorize.net/xml/v1/request.api',
            'deviceType'        => static::DEVICE_TYPE_UNKNOWN
        );
    }

    public function getApiLoginId()
    {
        return $this->getParameter('apiLoginId');
    }

    public function setApiLoginId($value)
    {
        return $this->setParameter('apiLoginId', $value);
    }

    public function getTransactionKey()
    {
        return $this->getParameter('transactionKey');
    }

    public function setTransactionKey($value)
    {
        return $this->setParameter('transactionKey', $value);
    }

    public function getDeveloperMode()
    {
        return $this->getParameter('developerMode');
    }

    public function setDeveloperMode($value)
    {
        return $this->setParameter('developerMode', $value);
    }

    public function setHashSecret($value)
    {
        return $this->setParameter('hashSecret', $value);
    }

    public function getHashSecret()
    {
        return $this->getParameter('hashSecret');
    }

    public function setEndpoints($endpoints)
    {
        $this->setParameter('liveEndpoint', $endpoints['live']);
        return $this->setParameter('developerEndpoint', $endpoints['developer']);
    }

    public function getLiveEndpoint()
    {
        return $this->getParameter('liveEndpoint');
    }

    public function setLiveEndpoint($value)
    {
        return $this->setParameter('liveEndpoint', $value);
    }

    public function getDeveloperEndpoint()
    {
        return $this->getParameter('developerEndpoint');
    }

    public function setDeveloperEndpoint($value)
    {
        return $this->setParameter('developerEndpoint', $value);
    }

    public function getDuplicateWindow()
    {
        return $this->getParameter('duplicateWindow');
    }

    public function setDuplicateWindow($value)
    {
        return $this->setParameter('duplicateWindow', $value);
    }

    public function getDeviceType()
    {
        return $this->getParameter('deviceType');
    }

    /**
     * Sets the type of device used to collect the credit card data.
     * A device type is required for card present transactions.
     *
     * 1 = Unknown
     * 2 = Unattended Terminal
     * 3 = Self Service Terminal
     * 4 = Electronic Cash Register
     * 5 = Personal Computer-Based Terminal
     * 6 = AirPay
     * 7 = Wireless POS
     * 8 = Website
     * 9 = Dial Terminal
     * 10 = Virtual Terminal
     *
     * @see http://developer.authorize.net/api/reference/#payment-transactions-charge-a-credit-card
     * @param $value
     * @return $this
     */
    public function setDeviceType($value)
    {
        return $this->setParameter('deviceType', $value);
    }

    /**
     * @param array $parameters
     * @return AIMAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMCaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMVoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMVoidRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMRefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMRefundRequest', $parameters);
    }
}
