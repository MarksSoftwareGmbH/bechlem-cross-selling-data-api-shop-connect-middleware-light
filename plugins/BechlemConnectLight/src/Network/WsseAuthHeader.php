<?php
declare(strict_types=1);

/* 
 * MIT License
 *
 * Copyright (c) 2018-present, Marks Software GmbH (https://www.marks-software.de/)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace BechlemConnectLight\Network;

use SoapHeader;
use SoapVar;
use stdClass;

/**
 * Class WsseAuthHeader
 *
 * @package BechlemConnectLight\Network
 */
class WsseAuthHeader extends SoapHeader
{

    /**
     * Connection status
     *
     * @var string
     */
    public $wssNs = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

    /**
     * WsseAuthHeader constructor.
     *
     * @param $user
     * @param $pass
     * @param string|null $ns
     */
    public function __construct($user, $pass, $ns = null)
    {
        if ($ns) {
            $this->wssNs = $ns;
        }
        $auth = new stdClass();
        $auth->Username = new SoapVar($user, XSD_STRING, null, $this->wssNs, null, $this->wssNs);
        $auth->Password = new SoapVar($pass, XSD_STRING, null, $this->wssNs, null, $this->wssNs);
        $usernameToken = new stdClass();

        $usernameToken->UsernameToken = new SoapVar($auth, SOAP_ENC_OBJECT, null, $this->wssNs, 'UsernameToken', $this->wssNs);
        $securityService = new SoapVar(
            new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, $this->wssNs, 'UsernameToken', $this->wssNs),
            SOAP_ENC_OBJECT,
            null,
            $this->wssNs,
            'Security',
            $this->wssNs
        );

        parent::__construct($this->wssNs, 'Security', $securityService, true);
    }
}
