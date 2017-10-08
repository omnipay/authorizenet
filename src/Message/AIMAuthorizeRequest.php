<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;

/**
 * Authorize.Net AIM Authorize Request
 */
class AIMAuthorizeRequest extends AIMAbstractRequest
{
    protected $action = 'authOnlyTransaction';

    public function getData()
    {
        $this->validate('amount');
        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getAmount();
        $this->addPayment($data);
        $this->addSolutionId($data);
        $this->addBillingData($data);
        $this->addCustomerIP($data);
        $this->addRetail($data);
        $this->addTransactionSettings($data);

        return $data;
    }

    protected function addPayment(\SimpleXMLElement $data)
    {
        /**
         * @link http://developer.authorize.net/api/reference/features/acceptjs.html Documentation on opaque data
         */
        if ($this->getOpaqueDataDescriptor() && $this->getOpaqueDataValue()) {
            $data->transactionRequest->payment->opaqueData->dataDescriptor = $this->getOpaqueDataDescriptor();
            $data->transactionRequest->payment->opaqueData->dataValue = $this->getOpaqueDataValue();
            return;
        }

        // The CreditCard object must be present.
        $this->validate('card');

        /** @var CreditCard $card */
        $card = $this->getCard();

        if ($card->getTracks()) {
            // Card present
            if ($track1 = $card->getTrack1()) {
                $data->transactionRequest->payment->trackData->track1 = $track1;
            } elseif ($track2 = $card->getTrack2()) {
                $data->transactionRequest->payment->trackData->track2 = $track2;
            }
        } else {
            // Card not present.

            // Validate sufficient card details have been supplied.
            $card->validate();

            $data->transactionRequest->payment->creditCard->cardNumber = $card->getNumber();
            $data->transactionRequest->payment->creditCard->expirationDate = $card->getExpiryDate('my');
            $data->transactionRequest->payment->creditCard->cardCode = $card->getCvv();
        }
    }

    protected function addCustomerIP(\SimpleXMLElement $data)
    {
        $ip = $this->getClientIp();
        if (!empty($ip)) {
            $data->transactionRequest->customerIP = $ip;
        }
    }

    protected function addRetail(\SimpleXMLElement $data)
    {
        if ($this->isCardPresent()) {
            // Retail element is required for card present transactions
            $data->transactionRequest->retail->marketType = 2;
            $data->transactionRequest->retail->deviceType = $this->getDeviceType();
        }
    }
}
