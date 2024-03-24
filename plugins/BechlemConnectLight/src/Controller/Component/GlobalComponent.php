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
namespace BechlemConnectLight\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Psr\Log\LogLevel;
use Exception;
use Cake\Utility\Text;

/**
 * Global component
 *
 * Class GlobalComponent
 * @package BechlemConnectLight\Controller\Component
 */
class GlobalComponent extends Component
{
    /**
     * Constructor hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @param array $config The configuration settings provided to this component.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    /**
     * Callback for Controller startup event.
     *
     * @param \Cake\Event\Event $event Event instance.
     */
    public function startup(Event $event)
    {
        $Controller = $event->getSubject();
    }

    /**
     * Csv to array
     *
     * @param string|null $filename
     * @param string $delimiter
     * @param string $enclosure
     * @return array|null
     */
    public function csvToArray($filename = null, $delimiter = ',', $enclosure = '"')
    {
        $array = [];
        $rowCount = 0;
        if (($handle = fopen($filename, 'r')) !== false) {
            $maxLineLength = defined('MAX_LINE_LENGTH')? MAX_LINE_LENGTH: 10000;
            $header = fgetcsv($handle, $maxLineLength, $delimiter, $enclosure);
            // Clean array keys for CakePHP conventions
            $keys = [];
            foreach ($header as $key) {
                $keys[] = Text::slug(strtolower($key), '_');
            }
            $headerColCount = count($header);
            while (($row = fgetcsv($handle, $maxLineLength, $delimiter, $enclosure)) !== false) {
                $rowColCount = count($row);
                if ($rowColCount == $headerColCount) {
                    // Combine keys and vals
                    $entry = array_combine($keys, $row);
                    $array[] = $entry;
                }
                $rowCount++;
            }
            fclose($handle);
        } else {
            Log::write(LogLevel::ERROR, __d('bechlem_connect_light', 'Could not read {filename}', ['filename' => $filename]));

            return null;
        }

        return $array;
    }

    /**
     * Log Request method
     *
     * @param object|null $controller
     * @param string|null $type
     * @param string|null $message
     * @param array $data
     * @return void
     */
    public function logRequest(object $controller = null, string $type = null, string $message = null, array $data = [])
    {
        try {
            $Logs = TableRegistry::getTableLocator()->get('BechlemConnectLight.Logs');
            $Logs->createLog(
                $controller->getRequest()->getMethod(),
                $type,
                $message,
                $controller->getRequest()->clientIp(),
                $controller->getRequest()->getUri(),
                $data
            );
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);
        }
    }

    /**
     * Captcha method.
     *
     * @param object|null $controller
     * @return bool
     */
    public function captcha(object $controller = null)
    {
        $digitOne = mt_rand(1,20);
        $digitTwo = mt_rand(1,20);

        $controller
            ->getRequest()
            ->getSession()
            ->write('BechlemConnectLight.Captcha.digit_one', $digitOne);
        $controller
            ->getRequest()
            ->getSession()
            ->write('BechlemConnectLight.Captcha.digit_two', $digitTwo);
        $controller
            ->getRequest()
            ->getSession()
            ->write('BechlemConnectLight.Captcha.result', $digitOne + $digitTwo);

        return true;
    }
}
