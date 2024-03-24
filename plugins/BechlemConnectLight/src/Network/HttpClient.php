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
use Cake\Http\Client\Response;
use Cake\Log\Log;
use Psr\Log\LogLevel;
use Cake\Http\Client as Client;

/**
 * Client Override
 */
class HttpClient extends Client
{

    /**
     * Helper method for doing non-GET requests.
     *
     * @param string $method HTTP method.
     * @param string $url URL to request.
     * @param mixed $data The request body.
     * @param array $options The options to use. Contains auth, proxy, etc.
     * @return \Cake\Http\Client\Response
     */
    protected function _doRequest(string $method, string $url, $data, $options): Response
    {
        if (Configure::read('debug') === true) {
            Log::write(LogLevel::INFO, $method);
            Log::write(LogLevel::INFO, $url);
            Log::write(LogLevel::INFO, 'data', $data);
            Log::write(LogLevel::INFO, 'options', $options);
        }

        return parent::_doRequest($method, $url, $data, $options);
    }
}
