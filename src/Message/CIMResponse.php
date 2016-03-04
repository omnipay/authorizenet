<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net CIM transaction Response
 */
class CIMResponse extends CIMAbstractResponse
{
    /**
     * For Error codes: @see https://developer.authorize.net/api/reference/responseCodes.html
     */
    const ERROR_RESPONSE_CODE_CANNOT_ISSUE_CREDIT = '54';

    protected $xmlRootElement = 'createCustomerProfileTransactionResponse';

    public function getTransactionReference()
    {
        if (!$this->isSuccessful()) {
            return null;
        }

        $transRef = null;
        if (isset($this->data['directResponse'])) {
            $transRef = array();
            // In case of a successful transaction, a "directResponse" element is present
            $directResponse = explode(',', (string)$this->data['directResponse']);
            // Required for capturing an authorized transaction
            $transRef['approvalCode'] = $directResponse[4];
            // Required for refund a transaction
            $transRef['transId'] = $directResponse[6];

            // Save the card reference also as it is needed for making further transactions.
            // This card reference is got from the request. (transaction response does not have it)
            $transRef['cardReference'] = $this->request->getCardReference();
            $transRef = json_encode($transRef);
        }

        return $transRef;
    }
}
