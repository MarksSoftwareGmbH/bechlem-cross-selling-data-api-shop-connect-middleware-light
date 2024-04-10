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
namespace BechlemConnectLight\Config;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use BechlemConnectLight\Event\EventManager;
use BechlemConnectLight\Menu\MenuManager;
use BechlemConnectLight\Utility\BechlemConnectLight;

BechlemConnectLight::hookComponent('*', 'BechlemConnectLight.Global');
BechlemConnectLight::hookComponent('*', 'BechlemConnectLight.Locale');
BechlemConnectLight::hookComponent('*', 'BechlemConnectLight.Setting');
BechlemConnectLight::hookComponent('*', 'BechlemConnectLight.User');

// @codingStandardsIgnoreStart
// Make sure that the BechlemConnectLight event manager is the global one
EventManager::instance();
MenuManager::instance();

/**
 * Failed login attempts
 *
 * Default is 3 failed login attempts in every 5 minutes
 */
$cacheConfig = array_merge(
    Cache::getConfig('default'),
    ['groups' => ['users']]
);
$failedLoginDuration = 300;
Configure::write('BechlemConnectLight.failed_login_limit', 3);
Configure::write('BechlemConnectLight.failed_login_duration', $failedLoginDuration);
Cache::setConfig('bechlem_connect_light_users_login',
    array_merge(
        $cacheConfig,
        [
            'duration' => '+' . $failedLoginDuration . ' seconds',
            'groups' => ['users'],
        ]
    )
);

Configure::write('CakePdf', ['engine' => 'CakePdf.Tcpdf']);

// Load all EventHandlers defined in BechlemConnectLight\Config\events and later other activated Plugins
EventManager::loadListeners();
MenuManager::loadListeners();
// @codingStandardsIgnoreEnd
