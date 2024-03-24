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
namespace BechlemConnectLight\Event;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;

/**
 * UsersEventHandler
 *
 * @package BechlemConnectLight\Event
 */
class UsersEventHandler implements EventListenerInterface
{

    /**
     * implementedEvents
     */
    public function implementedEvents(): array
    {
        return [
            'Controller.Admin.Users.beforeLogin' => [
                'callable' => 'onBeforeLogin',
            ],
            'Controller.Admin.Users.onLoginSuccess' => [
                'callable' => 'onLoginSuccess',
            ],
            'Controller.Admin.Users.onLoginFailure' => [
                'callable' => 'onLoginFailure',
            ],
            'Controller.Admin.Users.onEditSuccess' => [
                'callable' => 'onEditSuccess',
            ],
            'Controller.Users.beforeLogin' => [
                'callable' => 'onBeforeLogin',
            ],
            'Controller.Users.onLoginSuccess' => [
                'callable' => 'onLoginSuccess',
            ],
            'Controller.Users.onLoginFailure' => [
                'callable' => 'onLoginFailure',
            ],
            'Controller.Users.onEditSuccess' => [
                'callable' => 'onEditSuccess',
            ],
        ];
    }

    /**
     * On before login event method.
     *
     * @param Event $event
     * @return bool
     */
    public function onBeforeLogin(Event $event)
    {
        if (empty($event->getSubject()->getRequest()->getData())) {
            return true;
        }
        $cacheName = 'bechlem_connect_light_auth_failed_' . $event->getSubject()->getRequest()->getData('username');
        $cacheValue = Cache::read($cacheName, 'bechlem_connect_light_users_login');

        if ($cacheValue == Configure::read('BechlemConnectLight.failed_login_limit')) {
            $event->getSubject()->Flash->set(
                __d(
                    'bechlem_connect_light',
                    'You have reached maximum limit for failed login attempts. Please, try again after a few minutes.'
                ),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );

            return false;
        }

        return true;
    }

    /**
     * On login success event method.
     *
     * @param Event $event
     * @return bool
     */
    public function onLoginSuccess(Event $event)
    {
        if (empty($event->getSubject()->getRequest()->getData())) {
            return true;
        }
        $cacheName = 'bechlem_connect_light_auth_failed_' . $event->getSubject()->getRequest()->getData('username');
        Cache::delete($cacheName, 'bechlem_connect_light_users_login');

        $user = $event->getData();
        if ($user['User']['id']) {
            $dateTime = DateTime::now();

            $Users = TableRegistry::getTableLocator()->get('BechlemConnectLight.Users');
            $user = $Users->get($user['User']['id'], contain: ['Roles', 'Locales', 'UserProfiles']);
            $user->last_login = $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $Users->save($user);

            // User blocked, logout and redirect
            if (isset($user->status) && ($user->status == 0)) {
                $event->getSubject()->Flash->set(
                    __d('bechlem_connect_light', 'Your user account is blocked. Please, contact our support.'),
                    ['element' => 'default', 'params' => ['class' => 'danger']]
                );

                return false;
            }

            if (isset($user->user_profile) && !empty($user->user_profile->image)) {
                $event->getSubject()->getRequest()->getSession()->write('Auth.User.avatar', $user->user_profile->image);
            }
            if (isset($user->locale->code) && !empty($user->locale->code)) {
                return $event->getSubject()->redirect([
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Locales',
                    'action'        => 'switchLocale',
                    'code'          => h($user->locale->code),
                ]);
            }
        }

        return true;
    }

    /**
     * On login failure event method.
     *
     * @param Event $event
     * @return bool
     */
    public function onLoginFailure(Event $event)
    {
        if (empty($event->getSubject()->getRequest()->getData())) {
            return true;
        }

        $cacheName = 'bechlem_connect_light_auth_failed_' . $event->getSubject()->getRequest()->getData('username');
        $cacheValue = Cache::read($cacheName, 'bechlem_connect_light_users_login');

        $newCacheValue = (int)$cacheValue + 1;

        Cache::write($cacheName, $newCacheValue, 'bechlem_connect_light_users_login');

        if ($cacheValue < Configure::read('BechlemConnectLight.failed_login_limit')) {
            $event->getSubject()->Flash->set(
                __d(
                    'bechlem_connect_light',
                    'You have {newCacheValue} failed login attempts. Please, try again.',
                    ['newCacheValue' => $newCacheValue]
                ),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );

            return false;
        }

        return true;
    }

    /**
     * On edit success event method.
     *
     * @param Event $event
     * @return bool
     */
    public function onEditSuccess(Event $event)
    {
        if (empty($event->getSubject()->getRequest()->getData())) {
            return true;
        }

        $user = $event->getData();
        if ($user['User']['id']) {

            $Users = TableRegistry::getTableLocator()->get('BechlemConnectLight.Users');
            $user = $Users->get($user['User']['id'], contain: ['Roles', 'Locales', 'UserProfiles']);

            // User blocked, logout and redirect
            if (isset($user->status) && ($user->status == 0)) {
                $event->getSubject()->Flash->set(
                    __d('bechlem_connect_light', 'Your user account is blocked. Please, contact our support.'),
                    ['element' => 'default', 'params' => ['class' => 'danger']]
                );

                return $event->getSubject()->redirect([
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Users',
                    'action'        => 'logout',
                ]);
            }

            if (isset($user->user_profile) && !empty($user->user_profile->image)) {
                $event->getSubject()->getRequest()->getSession()->write('Auth.User.avatar', $user->user_profile->image);
            }
            if (isset($user->locale->code) && !empty($user->locale->code)) {
                return $event->getSubject()->redirect([
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Locales',
                    'action'        => 'switchLocale',
                    'code'          => h($user->locale->code),
                ]);
            }
        }

        return true;
    }
}
