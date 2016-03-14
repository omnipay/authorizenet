<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\AuthorizeNet\Model\CardReference;
use Omnipay\AuthorizeNet\Model\TransactionReference;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Authorize.Net AIM Abstract Request
 */
abstract class AIMAbstractRequest extends AbstractRequest
{
    protected $requestType = 'createTransactionRequest';
    protected $action = null;

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

    public function getCustomerId()
    {
        return $this->getParameter('customerId');
    }

    public function setCustomerId($value)
    {
        return $this->setParameter('customerId', $value);
    }

    public function getHashSecret()
    {
        return $this->getParameter('hashSecret');
    }
    public function setHashSecret($value)
    {
        return $this->setParameter('hashSecret', $value);
    }

    public function setDuplicateWindow($value)
    {
        $this->setParameter('duplicateWindow', $value);
    }

    private function getDuplicateWindow()
    {
        return $this->getParameter('duplicateWindow'); // Maps x_duplicate_window
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

    public function getEndpoint()
    {
        return $this->getDeveloperMode() ? $this->getDeveloperEndpoint() : $this->getLiveEndpoint();
    }

    /**
     * @return TransactionReference
     */
    public function getTransactionReference()
    {
        return $this->getParameter('transactionReference');
    }

    public function setTransactionReference($value)
    {
        if (substr($value, 0, 1) === '{') {
            // Value is a complex key containing the transaction ID and other properties
            $transactionRef = new TransactionReference($value);
        } else {
            // Value just contains the transaction ID
            $transactionRef = new TransactionReference();
            $transactionRef->setTransId($value);
        }
        return $this->setParameter('transactionReference', $transactionRef);
    }

    /**
     * @param string|CardReference $value
     * @return AbstractRequest
     */
    public function setCardReference($value)
    {
        if (!($value instanceof CardReference)) {
            $value = new CardReference($value);
        }
        return parent::setCardReference($value);
    }

    /**
     * @param bool $serialize Determines whether the return value will be a string or object
     * @return string|CardReference
     */
    public function getCardReference($serialize = true)
    {
        $value = parent::getCardReference();
        if ($serialize) {
            $value = (string)$value;
        }
        return $value;
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

        return $this->response = new AIMResponse($this, $httpResponse->getBody());
    }

    /**
     * @return mixed|\SimpleXMLElement
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getBaseData()
    {
        $data = new \SimpleXMLElement('<' . $this->requestType . '/>');
        $data->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');
        $this->addAuthentication($data);
        $this->addReferenceId($data);
        $this->addTransactionType($data);
        return $data;
    }

    protected function addAuthentication(\SimpleXMLElement $data)
    {
        $data->merchantAuthentication->name = $this->getApiLoginId();
        $data->merchantAuthentication->transactionKey = $this->getTransactionKey();
    }

    protected function addReferenceId(\SimpleXMLElement $data)
    {
        $txnId = $this->getTransactionId();
        if (!empty($txnId)) {
            $data->refId = $this->getTransactionId();
        }
    }

    protected function addTransactionType(\SimpleXMLElement $data)
    {
        if (!$this->action) {
            // The extending class probably hasn't specified an "action"
            throw new InvalidRequestException();
        }
        $data->transactionRequest->transactionType = $this->action;
    }

    /**
     * Adds billing data to a partially filled request data object.
     *
     * @param \SimpleXMLElement $data
     *
     * @return \SimpleXMLElement
     */
    protected function addBillingData(\SimpleXMLElement $data)
    {
        /** @var mixed $req */
        $req = $data->transactionRequest;

        // Merchant assigned customer ID
        $customer = $this->getCustomerId();
        if (!empty($customer)) {
            $req->customer->id = $customer;
        }

        /** @var CreditCard $card */
        if ($card = $this->getCard()) {
            // A card is present, so include billing and shipping details
            $req->billTo->firstName = $card->getBillingFirstName();
            $req->billTo->lastName = $card->getBillingLastName();
            $req->billTo->company = $card->getBillingCompany();
            $req->billTo->address = trim($card->getBillingAddress1() . " \n" . $card->getBillingAddress2());
            $req->billTo->city = $card->getBillingCity();
            $req->billTo->state = $card->getBillingState();
            $req->billTo->zip = $card->getBillingPostcode();
            $req->billTo->country = $card->getBillingCountry();

            $req->shipTo->firstName = $card->getShippingLastName();
            $req->shipTo->lastName = $card->getShippingLastName();
            $req->shipTo->company = $card->getShippingCompany();
            $req->shipTo->address = trim($card->getShippingAddress1() . " \n" . $card->getShippingAddress2());
            $req->shipTo->city = $card->getShippingCity();
            $req->shipTo->state = $card->getShippingState();
            $req->shipTo->zip = $card->getShippingPostcode();
            $req->shipTo->country = $card->getShippingCountry();
        }

        return $data;
    }

    protected function addTestModeSetting(\SimpleXMLElement $data)
    {
        // Test mode setting
        $data->transactionRequest->transactionSettings->setting->settingName = 'testRequest';
        $data->transactionRequest->transactionSettings->setting->settingValue = $this->getTestMode() ? 'true' : 'false';

        return $data;
    }

    protected function addExtraOptions(\SimpleXMLElement $data)
    {
        if (!is_null($this->getDuplicateWindow())) {
            $extraOptions = $data->addChild('extraOptions');
            $node = dom_import_simplexml($extraOptions);
            $nodeOwner = $node->ownerDocument;
            $duplicateWindowStr = sprintf("x_duplicate_window=%s", $this->getDuplicateWindow());
            $node->appendChild($nodeOwner->createCDATASection($duplicateWindowStr));
        }
        return $data;
    }

    protected function isCardPresent()
    {
        // If the credit card has track data, then consider this a "card present" scenario
        return ($card = $this->getCard()) && $card->getTracks();
    }
}
