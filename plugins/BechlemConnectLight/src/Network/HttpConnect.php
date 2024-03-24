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

use Cake\Core\InstanceConfigTrait;
use Cake\Log\Log;
use BechlemConnectLight\Network\HttpClient;
use Exception;

/**
 * HttpConnect Model
 */
class HttpConnect
{

    use InstanceConfigTrait;

    /**
     * HttpClient instance
     *
     * @var object
     */
    public $client = null;

    /**
     * Connection status
     *
     * @var boolean
     */
    public $connected = false;

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'adapter' => 'Cake\Http\Client\Adapter\Stream',
        'host' => null,
        'port' => null,
        'scheme' => 'http',
        'timeout' => 30,
        'ssl_verify_peer' => true,
        'ssl_verify_peer_name' => true,
        'ssl_verify_depth' => 5,
        'ssl_verify_host' => true,
        'redirect' => false,
    ];

    /**
     * Constructor
     *
     * HttpConnect constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
        $this->connect();
    }

    /**
     * Connects to the Http host in the configuration
     *
     * @return bool
     */
    public function connect()
    {
        try {
            $this->client = new HttpClient($this->getConfig());
        } catch (Exception $ex) {
            Log::write('error', (string) $ex);
        }

        if ($this->client) {
            $this->connected = true;
        }

        return $this->connected;
    }

    /**
     * Query the Http host with the given method and parameters
     *
     * @param string $method
     * @param string|null $url
     * @param array $data
     * @param array $options
     * @return \Cake\Http\Client\Response|bool
     */
    public function sendRequest(string $method = 'GET', string $url = null, $data = [], $options = [])
    {
        if (!$this->connected) {
            $this->connect();
        }

        try {
            switch ($method) {
                case 'POST':
                    $result = $this->client->post($url, $data, $options);
                    break;
                case 'PUT':
                    $result = $this->client->put($url, $data, $options);
                    break;
                case 'DELETE':
                    $result = $this->client->delete($url, $data, $options);
                    break;
                case 'HEAD':
                    $result = $this->client->head($url, $data, $options);
                    break;
                case 'PATCH':
                    $result = $this->client->patch($url, $data, $options);
                    break;
                default:
                    $result = $this->client->get($url, $data, $options);
            }
        } catch (Exception $ex) {
            Log::write('error', (string) $ex);

            return false;
        }

        return $result;
    }
}
