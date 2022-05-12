<?php

namespace ustmaestro\goglobalapi;

use SoapClient;

/**
 * GoGlobal soap client
 *
 * Available client methods
 * @method MakeRequest(array $params = [])
 * @method MakeRequestCompressed(array $params = [])
 * @method MakeRequestCompressedNewAsync(array $params = [])
 * @method MakeRequestNewAsync(array $params = [])
 */
class GGSoapClient extends SoapClient
{
}
