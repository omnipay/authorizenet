<?php


namespace Omnipay\AuthorizeNet\Message;


use Illuminate\Support\Collection;

class AIMUnsettledTransactionsRequest extends AIMAbstractRequest
{
    protected $requestType = 'getUnsettledTransactionListRequest';

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = new \SimpleXMLElement('<' . $this->requestType . '/>');

        $data->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');
        $this->addAuthentication($data);
        $this->addReferenceId($data);
        if ($this->getStatus()) {
            $data->status = $this->getStatus();
        }

        return $data;
    }

    public function getStatus()
    {
        return $this->getParameter('status');
    }

    public function setStatus($value)
    {
        return $this->setParameter('status', $value);
    }

    public function send()
    {
        $response = parent::send();

        if (isset($response->getData()->messages) && (string)$response->getData()->messages->resultCode === 'Ok') {
            $transactions = new Collection();
            foreach ((array) $response->getData()->transactions as $node) {
                if (is_array($node)) {
                    foreach ($node as $item) {
                        $transactions->push(xml2array($item));
                    }
                } else {
                    $transactions->push(xml2array($node));
                }
            }

            return $transactions;
        }

        return new Collection();
    }
}