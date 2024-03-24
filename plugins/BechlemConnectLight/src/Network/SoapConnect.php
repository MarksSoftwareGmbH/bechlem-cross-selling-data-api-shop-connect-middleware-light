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

use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Log\Log;
use Psr\Log\LogLevel;
use BechlemConnectLight\Network\SoapClient;
use SoapFault;

/**
 * SoapConnect Model
 */
class SoapConnect
{

    use InstanceConfigTrait;

    /**
     * SoapClient instance
     *
     * @var SoapClient
     */
    public $client = null;

    /**
     * Connection status
     *
     * @var boolean
     */
    public $connected = false;


    /**
     * Error
     *
     * @var string
     */
    public $error = null;

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'wsdl' => null,
        'userAgent' => 'SoapClient',
        'location' => '',
        'uri' => '',
        'login' => '',
        'password' => '',
        'authentication' => 'SOAP_AUTHENTICATION_`IC',
        'trace' => false,
        'exception' => false,
    ];

    /**
     * Constructor
     *
     * @param array $config An array defining the configuration settings
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
        $this->connect();
    }

    /**
     * Setup Configuration options
     *
     * @return array|bool
     */
    protected function _parseConfig()
    {
        if (!class_exists('SoapClient')) {
            $this->error = __d('bechlem_connect_light', 'Class SoapClient not found, please enable Soap extensions.');
            $this->showError();

            return false;
        }
        $opts = ['http' => ['user_agent' => $this->getConfig('userAgent')]];
        $context = stream_context_create($opts);
        $options = [
            'trace' => Configure::read('debug'),
            'stream_context' => $context,
            'cache_wsdl' => WSDL_CACHE_NONE
        ];
        if (!empty($this->getConfig('location'))) {
            $options['location'] = $this->getConfig('location');
        }
        if (!empty($this->getConfig('uri'))) {
            $options['uri'] = $this->getConfig('uri');
        }
        if (!empty($this->getConfig('login'))) {
            $options['login'] = $this->getConfig('login');
            $options['password'] = $this->getConfig('password');
            $options['authentication'] = $this->getConfig('authentication');
        }

        return $options;
    }

    /**
     * Connects to the SOAP server using the WSDL in the configuration
     *
     * @return bool True on success, false on failure
     */
    public function connect()
    {
        $options = $this->_parseConfig();
        try {
            $this->client = new SoapClient($this->getConfig('wsdl'), $options);
        } catch (SoapFault $fault) {
            $this->error = $fault->faultstring;
            $this->showError();
        }
        if ($this->client) {
            $this->connected = true;
        }

        return $this->connected;
    }

    /**
     * Sets the SoapClient instance to null
     *
     * @return bool True
     */
    public function close()
    {
        $this->client = null;
        $this->connected = false;

        return true;
    }

    /**
     * Returns the available SOAP methods
     *
     * @return array List of SOAP methods
     */
    public function listSources()
    {
        return $this->client->__getFunctions();
    }

    /**
     * Query the SOAP server with the given method and parameters
     *
     * @param string $action The WSDL Action
     * @param array $data The data array
     * @return mixed Returns the result on success, false on failure
     */
    public function sendRequest($action, $data)
    {
        $this->error = false;
        if (!$this->connected) {
            $this->connect();
        }
        try {
            $result = $this->client->__soapCall($action, $data);
        } catch (SoapFault $fault) {
            $this->error = $fault->faultstring;
            $this->showError();

            return false;
        }

        return $result;
    }

    /**
     * Returns the last SOAP response
     *
     * @return string The last SOAP response
     */
    public function getResponse()
    {
        return $this->client->__getLastResponse();
    }

    /**
     * Returns the last SOAP request
     *
     * @return string The last SOAP request
     */
    public function getRequest()
    {
        return $this->client->__getLastRequest();
    }

    /**
     * Shows an error message and outputs the SOAP result if passed
     *
     * @param string $result A SOAP result
     * @return void
     */
    public function showError($result = null)
    {
        Log::write(LogLevel::ERROR, $this->client->__getLastRequest());
        if (Configure::read('debug') === true) {
            if ($this->error) {
                trigger_error(
                    '<span style="color: #ff0; text-align: left;"><b>SOAP Error:</b>' . ' ' . $this->error . '</span>',
                    E_USER_WARNING
                );
            }
            if (!empty($result)) {
                echo sprintf("<p><b>Result:</b> %s </p>", $result);
            }
        }
    }
}
