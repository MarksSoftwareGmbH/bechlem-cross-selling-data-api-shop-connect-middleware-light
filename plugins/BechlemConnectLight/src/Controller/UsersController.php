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
namespace BechlemConnectLight\Controller;

use Cake\Event\EventInterface;
use Cake\I18n\DateTime;
use BechlemConnectLight\Controller\AppController;
use BechlemConnectLight\Utility\BechlemConnectLight;
use Cake\Event\Event;
use Cake\Utility\Hash;
use Cake\Utility\Text;

/**
 * Users Controller
 *
 * @property \BechlemConnectLight\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * Locale
     *
     * @var string
     */
    private string $locale;

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     * @link https://book.cakephp.org/4/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $session = $this->getRequest()->getSession();
        $this->locale = $session->check('Locale.code')? $session->read('Locale.code'): 'en_US';
    }

    /**
     * Register method
     *
     * @return \Cake\Http\Response|void
     */
    public function register()
    {
        // Get session object
        $session = $this->getRequest()->getSession();

        $dateTime = DateTime::now();

        $user = $this->Users->newEmptyEntity();
        if ($this->getRequest()->is('post')) {

            $postData = $this->getRequest()->getData();

            if (
                $session->read('BechlemConnectLight.Captcha.result') !=
                $postData['captcha_result']
            ) {
                return $this->redirect($this->referer());
            }
            unset($postData['captcha_result']);

            $user = $this->Users->patchEntity(
                $user,
                Hash::merge(
                    $postData,
                    [
                        'role_id'           => 4, // Public
                        'status'            => 0, // Deactivated
                        'token'             => Text::uuid(),
                        'activation_date'   => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                    ]
                )
            );
            BechlemConnectLight::dispatchEvent('Controller.Users.beforeRegister', $this, ['User' => $user]);
            if ($this->Users->save($user)) {
                BechlemConnectLight::dispatchEvent('Controller.Users.onRegisterSuccess', $this, ['User' => $user]);
                if ($this->Users->sendRegisterConfirmationEmail($user, 'default', 'html')) {
                    $this->Flash->set(
                        __d('bechlem_connect_light', 'You have successfully registered. An confirmation email with further instructions has been sent to you.'),
                        ['element' => 'default', 'params' => ['class' => 'success']]
                    );
                } else {
                    $this->Flash->set(
                        __d('bechlem_connect_light',
                            'You have successfully registered. An confirmation email with further instructions could not be sent to you.'),
                        ['element' => 'default', 'params' => ['class' => 'error']]
                    );
                }

                if ($session->check('BechlemConnectLight.Captcha')) {
                    $session->delete('BechlemConnectLight.Captcha');
                    return $this->redirect(['action' => 'login']);
                }
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Users.onRegisterFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'You could not register. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $this->Global->captcha($this);

        $this->set('user', $user);
    }

    /**
     * Login method
     *
     * @return bool|\Cake\Http\Response|void
     */
    public function login()
    {
        if ($this->getRequest()->is('post')) {
            $event = BechlemConnectLight::dispatchEvent('Controller.Users.beforeLogin', $this, []);
            if ($event->isStopped()) {
                return $this->redirect(['action' => 'login']);
            }

            /**
             * Use the configured authentication adapters, and attempt to identify the user
             * by credentials contained in $request.
             *
             * Triggers `Auth.afterIdentify` event which the authenticate classes can listen
             * to.
             *
             * @return array|false User record data, or false, if the user could not be identified.
             */
            $user = $this->Auth->identify();
            if ($user) {
                /**
                 * Set provided user info to storage as logged in user.
                 *
                 * The storage class is configured using `storage` config key or passing
                 * instance to AuthComponent::storage().
                 *
                 * @param array|\ArrayAccess $user User data.
                 * @return void
                 * @link https://book.cakephp.org/4/en/controllers/components/authentication.html#identifying-users-and-logging-them-in
                 */
                $this->Auth->setUser($user);

                $event = BechlemConnectLight::dispatchEvent('Controller.Users.onLoginSuccess', $this, ['User' => $user]);
                if ($event->isStopped()) {
                    $this->getRequest()->getSession()->write('Auth.User.blocked', 1);

                    return $this->redirect(['action' => 'login']);
                }

                $this->Flash->set(
                    __d('bechlem_connect_light', 'You have successfully signed in.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $event = BechlemConnectLight::dispatchEvent('Controller.Users.onLoginFailure', $this, []);
                if ($event->isStopped()) {
                    return $this->redirect(['action' => 'login']);
                }
                $this->Flash->set(
                    __d('bechlem_connect_light', 'You could not login. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );

                return $this->redirect(['action' => 'login']);
            }
        }
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|void
     */
    public function logout()
    {
        BechlemConnectLight::dispatchEvent('Controller.Users.beforeLogout', $this, []);
        $this->Flash->set(
            __d('bechlem_connect_light', 'You have successfully signed out.'),
            ['element' => 'default', 'params' => ['class' => 'success']]
        );

        return $this->redirect($this->Auth->logout());
    }

    /**
     * Forgot method
     *
     * @return \Cake\Http\Response|null
     */
    public function forgot()
    {
        // Get session object
        $session = $this->getRequest()->getSession();

        if ($this->getRequest()->is('post')) {
            $postData = $this->getRequest()->getData();

            $session->write('BechlemConnectLight.User', $postData);

            if (
                $session->read('BechlemConnectLight.Captcha.result') !=
                $this->getRequest()->getData('captcha_result')
            ) {
                return $this->redirect($this->referer());
            }
            unset($postData['captcha_result']);

            BechlemConnectLight::dispatchEvent('Controller.Users.beforeForgot', $this, []);

            $user = $this->Users
                ->find()
                ->where([
                    'username'  => $postData['username'],
                    'email'     => $postData['email'],
                    'status'    => 1,
                ])
                ->first();
            if (!$user) {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'Invalid username, email or account blocked. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );

                return $this->redirect(['action' => 'forgot']);
            }

            $resetToken = $this->Users->resetToken($user);
            if (!$resetToken) {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'An error occurred. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }

            $resetUrl = [
                '_full'         => true,
                'plugin'        => 'BechlemConnectLight',
                'controller'    => 'Users',
                'action'        => 'reset',
                'username'      => isset($user->username)? $user->username: '',
                'token'         => isset($user->token)? $user->token: '',
            ];
            if ($this->Users->sendResetPasswordEmail($user, $resetUrl, 'default', 'html')) {
                BechlemConnectLight::dispatchEvent('Controller.Users.onForgotSuccess', $this, ['User' => $user]);

                $this->Flash->set(
                    __d('bechlem_connect_light', 'An email with further instructions has been sent to you.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );
                if ($session->check('BechlemConnectLight.User')) {
                    $session->delete('BechlemConnectLight.User');
                    return $this->redirect(['action' => 'login']);
                }
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Users.onForgotFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'An email with further instructions could not be sent to you. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }

            return $this->redirect($this->referer());
        }

        $this->Global->captcha($this);
    }

    /**
     * Reset method
     *
     * @param string|null $username
     * @param string|null $token
     *
     * @return \Cake\Http\Response|null
     */
    public function reset(string $username = null, string $token = null)
    {
        // Get session object
        $session = $this->getRequest()->getSession();

        $user = $this->Users
            ->find('byToken', options: ['username' => $username, 'token' => $token])
            ->first();
        if (!$user) {
            $this->Flash->set(
                __d('bechlem_connect_light', 'An error occurred. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );

            return $this->redirect(['action' => 'login']);
        }

        if ($this->getRequest()->is('post')) {

            $postData = $this->getRequest()->getData();

            if (
                $session->read('BechlemConnectLight.Captcha.result') !=
                $postData['captcha_result']
            ) {
                return $this->redirect($this->referer());
            }
            unset($postData['captcha_result']);

            BechlemConnectLight::dispatchEvent('Controller.Users.beforeReset', $this, ['User' => $user]);
            $user = $this->Users->changePasswordFromReset($user, $postData);
            if (!$user) {
                BechlemConnectLight::dispatchEvent('Controller.Users.onResetFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'An error occurred. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );

                return $this->redirect($this->referer());
            }
            BechlemConnectLight::dispatchEvent('Controller.Users.onResetSuccess', $this, ['User' => $user]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'Your password has been reset successfully.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );

            if ($session->check('BechlemConnectLight.Captcha')) {
                $session->delete('BechlemConnectLight.Captcha');
                return $this->redirect(['action' => 'login']);
            }
        }

        $this->Global->captcha($this);

        $this->set('user', $user);
    }

    /**
     * Welcome method
     *
     * @return bool|\Cake\Http\Response|void
     */
    public function welcome()
    {
        $session = $this->getRequest()->getSession();
        $userId = $session->check('Auth.User.id')? $session->read('Auth.User.id'): null;
        if (!empty($userId)) {
            $userAccount = $this->Users
                ->find()
                ->where(['Users.id' => $userId])
                ->first();
        } else {
            $userAccount = [];
        }

        $this->set('userAccount', $userAccount);
    }

    /**
     * Profile method
     *
     * @return \Cake\Http\Response|void
     */
    public function profile()
    {
        $session = $this->getRequest()->getSession();
        $userId = $session->check('Auth.User.id')? $session->read('Auth.User.id'): null;
        if (!empty($userId)) {
            $userAccount = $this->Users
                ->find()
                ->where(['Users.id' => $userId])
                ->first();
            $userProfile = $this->Users->UserProfiles
                ->find()
                ->where(['UserProfiles.user_id' => $userId])
                ->first();
        } else {
            $userAccount = [];
            $userProfile = [];
        }

        $this->set(compact(
            'userAccount',
            'userProfile'
        ));
    }

    /**
     * Edit method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function edit()
    {
        $session = $this->getRequest()->getSession();

        if ($this->getRequest()->is(['patch', 'post', 'put']) && !empty($this->getRequest()->getData())) {

            $userId = $session->check('Auth.User.id')? $session->read('Auth.User.id'): null;

            $user = $this->Users
                ->find()
                ->where(['Users.id' => $userId])
                ->first();
            if (!empty($user->id)) {
                $user = $this->Users->get($user->id);
                $user = $this->Users->patchEntity(
                    $user,
                    Hash::merge(
                        $this->getRequest()->getData(),
                        [
                            'locale_id'     => h($this->getRequest()->getData('locale_id')),
                            'name'          => h($this->getRequest()->getData('name')),
                            'email'         => h($this->getRequest()->getData('email')),
                        ]
                    )
                );
                BechlemConnectLight::dispatchEvent('Controller.Users.beforeEdit', $this, ['User' => $user]);
                if ($this->Users->save($user)) {
                    BechlemConnectLight::dispatchEvent('Controller.Users.onEditSuccess', $this, ['User' => $user]);
                    $this->Flash->set(
                        __d('bechlem_connect_light', 'The user account has been saved.'),
                        ['element' => 'default', 'params' => ['class' => 'success']]
                    );
                } else {
                    BechlemConnectLight::dispatchEvent('Controller.Users.onEditFailure', $this, ['User' => $user]);
                    $this->Flash->set(
                        __d('bechlem_connect_light', 'The user account could not be saved. Please, try again.'),
                        ['element' => 'default', 'params' => ['class' => 'error']]
                    );
                }
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Users.onEditFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user account could not be found. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }

            return $this->redirect($this->referer());
        }

        return $this->redirect($this->referer());
    }

    /**
     * Deactivate method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function deactivate()
    {
        $session = $this->getRequest()->getSession();

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {

            $userId = $session->check('Auth.User.id')? $session->read('Auth.User.id'): null;
            $user = $this->Users
                ->find()
                ->where(['Users.id' => $userId])
                ->first();
            if (!empty($user->id)) {
                $user = $this->Users->get($user->id);
                $user = $this->Users->patchEntity(
                    $user,
                    [
                        'id'        => $user->id,
                        'status'    => 0,
                    ]
                );
                BechlemConnectLight::dispatchEvent('Controller.Users.beforeDeactivate', $this, ['User' => $user]);
                if ($this->Users->save($user)) {
                    BechlemConnectLight::dispatchEvent('Controller.Users.onDeactivateSuccess', $this, ['User' => $user]);
                    $this->Flash->set(
                        __d('bechlem_connect_light', 'The user account has been deactivated.'),
                        ['element' => 'default', 'params' => ['class' => 'success']]
                    );
                } else {
                    BechlemConnectLight::dispatchEvent('Controller.Users.onDeactivateFailure', $this, ['User' => $user]);
                    $this->Flash->set(
                        __d('bechlem_connect_light', 'The user account could not be deactivated. Please, try again.'),
                        ['element' => 'default', 'params' => ['class' => 'error']]
                    );
                }
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Users.onDeactivateFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user account could not be found. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }

            return $this->redirect(['action' => 'profile']);
        }

        return $this->redirect($this->referer());
    }
}
