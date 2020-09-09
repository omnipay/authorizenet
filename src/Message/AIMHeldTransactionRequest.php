<?php


namespace Omnipay\AuthorizeNet\Message;


class AIMHeldTransactionRequest extends AIMAbstractRequest
{
    protected $requestType = 'updateHeldTransactionRequest';

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = new \SimpleXMLElement('<' . $this->requestType . '/>');

        $data->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');
        $this->addAuthentication($data);
        $this->addReferenceId($data);
        $data->heldTransactionRequest->action = $this->getAction();
        $data->heldTransactionRequest->refTransId = $this->getTransId();

        return $data;
    }

    public function getAction()
    {
        return $this->getParameter('action');
    }

    public function setAction($value)
    {
        return $this->setParameter('action', $value);
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