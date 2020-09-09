<?php


namespace Omnipay\AuthorizeNet\Message;


class AIMTransactionDetailsRequest extends AIMAbstractRequest
{
    protected $requestType = 'getTransactionDetailsRequest';

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = new \SimpleXMLElement('<' . $this->requestType . '/>');

        $data->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');
        $this->addAuthentication($data);
        $this->addReferenceId($data);
        $data->transId = $this->getTransId();

        return $data;
    }

    public function getTransId()
    {
        return $this->getParameter('transId');
    }

    public function setTransId($value)
    {
        return $this->setParameter('transId', $value);
    }
}