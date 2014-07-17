<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net AIM Authorize Request
 */
class CPPurchaseRequest extends CPAuthorizeRequest
{
	protected $liveEndpoint = 'https://cardpresent.authorize.net/gateway/transact.dll';
	protected $action       = 'AUTH_CAPTURE';
}
