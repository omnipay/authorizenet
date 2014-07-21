<?php

namespace Omnipay\AuthorizeNet;

/**
 * Authorize.Net CP Class
 */
class CPGateway extends AIMGateway
{
    protected $liveEndpoint = 'https://cardpresent.authorize.net/gateway/transact.dll';

    public function getName()
    {
        return 'Authorize.Net CP';
    }

    public function getDefaultParameters()
    {
        $parameters = parent::getDefaultParameters();

        $parameters['cpVersion']  = "1.0";
        $parameters['marketType'] = "2";
        $parameters['deviceType'] = "5"; // PC Terminal

        return $parameters;
    }

    public function setCPVersion($value)
    {
        return $this->setParameter('cpVersion', $value);
    }

    public function getCPVersion()
    {
        return $this->getParameter('cpVersion');
    }

    public function setMarketType($value)
    {
        return $this->setParameter('marketType', $value);
    }

    public function getMarketType()
    {
        return $this->getParameter('marketType');
    }

    public function setDeviceType($value)
    {
        return $this->setParameter('deviceType', $value);
    }

    public function getDeviceType()
    {
        return $this->getParameter('deviceType');
    }

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CPAuthorizeRequest', $parameters);
    }

    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CPCaptureRequest', $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\CPPurchaseRequest', $parameters);
    }

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMVoidRequest', $parameters);
    }
}