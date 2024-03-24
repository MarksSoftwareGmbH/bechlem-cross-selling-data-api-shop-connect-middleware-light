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
namespace BechlemConnectLight\Controller\Admin;

use BechlemConnectLight\Controller\Admin\AppController;
use BechlemConnectLight\Utility\BechlemConnectLight;
use Cake\Event\EventInterface;
use Cake\Http\CallbackStream;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
     * Pagination
     *
     * @var array
     */
    public array $paginate = [
        'limit' => 25,
        'maxLimit' => 50,
        'sortableFields' => [
            'id',
            'role_id',
            'locale_id',
            'foreign_key',
            'username',
            'name',
            'email',
            'status',
            'activation_date',
            'last_login',
            'created',
            'modified',
            'Roles.title',
            'Locales.name',
        ],
        'order' => ['created' => 'DESC']
    ];

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
     * Login method
     *
     * @return bool|\Cake\Http\Response|null
     */
    public function login()
    {
        if ($this->getRequest()->is('post')) {
            $event = BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeLogin', $this, []);
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
                $event = BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onLoginSuccess', $this, ['User' => $user]);
                if ($event->isStopped()) {
                    return $this->redirect(['action' => 'login']);
                }

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

                $this->Flash->set(
                    __d('bechlem_connect_light', 'You have successfully signed in.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $event = BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onLoginFailure', $this, []);
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
     * @return \Cake\Http\Response|null
     */
    public function logout()
    {
        BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeLogout', $this, []);
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
            $session->write('BechlemConnectLight.Admin.User', $this->getRequest()->getData());

            $postData = $this->getRequest()->getData();

            if (
                $session->read('BechlemConnectLight.Captcha.result') !=
                $this->getRequest()->getData('captcha_result')
            ) {
                return $this->redirect($this->referer());
            }
            unset($postData['captcha_result']);

            BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeForgot', $this, []);

            $user = $this->Users
                ->find()
                ->where([
                    'username' => $postData['username'],
                    'email' => $postData['email'],
                    'status' => 1,
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
                '_full' => true,
                'plugin' => 'BechlemConnectLight',
                'controller' => 'Users',
                'action' => 'reset',
                'username' => isset($user->username)? $user->username: '',
                'token' => isset($user->token)? $user->token: '',
            ];
            if ($this->Users->sendResetPasswordEmail($user, $resetUrl, 'default', 'html')) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onForgotSuccess', $this, ['User' => $user]);

                $this->Flash->set(
                    __d('bechlem_connect_light', 'An email with further instructions has been sent to you.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );
                if ($session->check('BechlemConnectLight.Admin.User')) {
                    $session->delete('BechlemConnectLight.Admin.User');
                    return $this->redirect(['action' => 'login']);
                }
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onForgotFailure', $this, ['User' => $user]);
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
                $this->getRequest()->getData('captcha_result')
            ) {
                return $this->redirect($this->referer());
            }
            unset($postData['captcha_result']);

            BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeReset', $this, ['User' => $user]);
            $user = $this->Users->changePasswordFromReset($user, $this->getRequest()->getData());
            if (!$user) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onResetFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'An error occurred. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );

                return $this->redirect($this->referer());
            }
            BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onResetSuccess', $this, ['User' => $user]);
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
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Users
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->contain([
                'Locales',
                'Roles',
            ]);

        $roles = $this->Users->Roles->find('list');

        $locales = $this->Users->Locales
            ->find('list',
                conditions: ['Locales.status' => 1],
                order: ['Locales.weight' => 'ASC'],
                keyField: 'id',
                valueField: 'name',
            )
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeIndexRender', $this, [
            'Query' => $query,
            'Roles' => $roles,
            'Locales' => $locales,
        ]);

        $this->set('users', $this->paginate($query));
        $this->set(compact('roles', 'locales'));
    }

    /**
     * Profile method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function profile(int $id = null)
    {
        // Get session object
        $session = $this->getRequest()->getSession();

        // Auth user can enter just his own profile || Admin can enter the view
        if (!($session->read('Auth.User.id') == $id)) {
            $this->Flash->set(
                __d('bechlem_connect_light', 'You are not allowed to view this profile.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );

            return $this->redirect(['action' => 'index']);
        }

        $user = $this->Users->get($id, contain: [
            'Locales' => function ($q) {
                return $q->orderBy(['Locales.weight' => 'ASC']);
            },
            'Roles' => function ($q) {
                return $q->orderBy(['Roles.title' => 'ASC']);
            }
        ]);

        $Countries = TableRegistry::getTableLocator()->get('BechlemConnectLight.Countries');
        $countries = $Countries
            ->find('list',
                conditions: ['Countries.status' => 1],
                order: ['Countries.name' => 'ASC'],
                keyField: 'id',
                valueField: 'name',
            )
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeProfileRender', $this, [
            'User' => $user,
            'Countries' => $countries,
        ]);

        $this->set(compact('user', 'countries'));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $user = $this->Users->get($id, contain: [
            'Locales',
            'Roles',
        ]);

        $Countries = TableRegistry::getTableLocator()->get('BechlemConnectLight.Countries');
        $countries = $Countries
            ->find('list',
                conditions: ['Countries.status' => 1],
                order: ['Countries.name' => 'ASC'],
                keyField: 'id',
                valueField: 'name',
            )
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeViewRender', $this, [
            'User' => $user,
            'Countries' => $countries,
        ]);

        $this->set(compact('user', 'countries'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $user = $this->Users->patchEntity(
                $user,
                Hash::merge(
                    $this->getRequest()->getData(),
                    ['token' => Text::uuid(), 'activation_date' => date('Y-m-d H:i:s')]
                ),
                ['associated' => ['UserProfiles']]
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeAdd', $this, ['User' => $user]);
            if ($this->Users->save($user)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onAddSuccess', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onAddFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $roles = $this->Users->Roles->find('list',
            order: ['Roles.title' => 'ASC'],
            keyField: 'id',
            valueField: 'title',
        );

        $locales = $this->Users->Locales->find('list',
            order: ['Locales.weight' => 'ASC'],
            keyField: 'id',
            valueField: 'name',
        );

        $Countries = TableRegistry::getTableLocator()->get('BechlemConnectLight.Countries');
        $countries = $Countries
            ->find('list',
                conditions: ['Countries.status' => 1],
                order: ['Countries.name' => 'ASC'],
                keyField: 'id',
                valueField: 'name',
            )
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeAddRender', $this, [
            'User' => $user,
            'Roles' => $roles,
            'Locales' => $locales,
            'Countries' => $countries,
        ]);

        $this->set(compact('user', 'locales', 'roles', 'countries'));
    }

    /**
     * Edit method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function edit(int $id = null)
    {
        $user = $this->Users->get($id, contain: [
            'Locales',
            'Roles',
            'UserProfiles',
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity(
                $user,
                $this->getRequest()->getData(),
                ['associated' => ['UserProfiles']]
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeEdit', $this, ['User' => $user]);
            if ($this->Users->save($user)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onEditSuccess', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onEditFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $roles = $this->Users->Roles->find('list', order: ['Roles.title' => 'ASC'], keyField: 'id', valueField: 'title');

        $locales = $this->Users->Locales->find('list', order: ['Locales.weight' => 'ASC'], keyField: 'id', valueField: 'name');

        $Countries = TableRegistry::getTableLocator()->get('BechlemConnectLight.Countries');
        $countries = $Countries
            ->find('list', conditions: ['Countries.status' => 1], order: ['Countries.name' => 'ASC'], keyField: 'id', valueField: 'name')
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeEditRender', $this, [
            'User' => $user,
            'Roles' => $roles,
            'Locales' => $locales,
            'Countries' => $countries,
        ]);

        $this->set(compact('user', 'locales', 'roles', 'countries'));
    }

    /**
     * Reset password method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function resetPassword(int $id = null)
    {
        $user = $this->Users->get($id);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeResetPassword', $this, ['User' => $user]);
            if ($this->Users->changePasswordFromReset($user, $this->getRequest()->getData())) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onResetPasswordSuccess', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user password has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onResetPasswordFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user password could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeResetPasswordRender', $this, ['User' => $user]);

        $this->set('user', $user);
    }

    /**
     * Delete method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function delete(int $id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id, contain: ['Roles']);

        // User Admin can not be deleted!
        if ($user->role->title === 'Admin') {
            $this->Flash->set(
                __d('bechlem_connect_light', 'You are not allowed to delete this user.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );

            return $this->redirect(['action' => 'index']);
        }
        BechlemConnectLight::dispatchEvent('Controller.Admin.Users.beforeDelete', $this, ['User' => $user]);
        if ($this->Users->delete($user)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onDeleteSuccess', $this, ['User' => $user]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The user has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Users.onDeleteFailure', $this, ['User' => $user]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The user could not be deleted. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Export xlsx method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportXlsx()
    {
        $users = $this->Users->find('all');
        $header = $this->Users->tableColumns;

        $usersArray = [];
        foreach($users as $user) {
            $userArray = [];
            $userArray['id'] = $user->id;
            $userArray['role_id'] = $user->role_id;
            $userArray['locale_id'] = $user->locale_id;
            $userArray['foreign_key'] = $user->foreign_key;
            $userArray['username'] = $user->username;
            $userArray['name'] = $user->name;
            $userArray['email'] = $user->email;
            $userArray['status'] = ($user->status == 1)? 1: 0;
            $userArray['activation_date'] = empty($user->activation_date)? NULL: $user->activation_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userArray['last_login'] = empty($user->last_login)? NULL: $user->last_login->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userArray['created'] = empty($user->created)? NULL: $user->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userArray['modified'] = empty($user->modified)? NULL: $user->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $usersArray[] = $userArray;
        }
        $users = $usersArray;

        $objSpreadsheet = new Spreadsheet();
        $objSpreadsheet->setActiveSheetIndex(0);

        $rowCount = 1;
        $colCount = 1;
        foreach ($header as $headerAlias) {
            $col = 'A';
            switch ($colCount) {
                case 2: $col = 'B'; break;
                case 3: $col = 'C'; break;
                case 4: $col = 'D'; break;
                case 5: $col = 'E'; break;
                case 6: $col = 'F'; break;
                case 7: $col = 'G'; break;
                case 8: $col = 'H'; break;
                case 9: $col = 'I'; break;
                case 10: $col = 'J'; break;
                case 11: $col = 'K'; break;
                case 12: $col = 'L'; break;
                case 13: $col = 'M'; break;
                case 14: $col = 'N'; break;
                case 15: $col = 'O'; break;
                case 16: $col = 'P'; break;
                case 17: $col = 'Q'; break;
                case 18: $col = 'R'; break;
                case 19: $col = 'S'; break;
                case 20: $col = 'T'; break;
                case 21: $col = 'U'; break;
                case 22: $col = 'V'; break;
                case 23: $col = 'W'; break;
                case 24: $col = 'X'; break;
                case 25: $col = 'Y'; break;
                case 26: $col = 'Z'; break;
            }

            $objSpreadsheet->getActiveSheet()->setCellValue($col . $rowCount, $headerAlias);
            $colCount++;
        }

        $rowCount = 1;
        foreach ($users as $dataEntity) {
            $rowCount++;

            $colCount = 1;
            foreach ($dataEntity as $dataProperty) {
                $col = 'A';
                switch ($colCount) {
                    case 2: $col = 'B'; break;
                    case 3: $col = 'C'; break;
                    case 4: $col = 'D'; break;
                    case 5: $col = 'E'; break;
                    case 6: $col = 'F'; break;
                    case 7: $col = 'G'; break;
                    case 8: $col = 'H'; break;
                    case 9: $col = 'I'; break;
                    case 10: $col = 'J'; break;
                    case 11: $col = 'K'; break;
                    case 12: $col = 'L'; break;
                    case 13: $col = 'M'; break;
                    case 14: $col = 'N'; break;
                    case 15: $col = 'O'; break;
                    case 16: $col = 'P'; break;
                    case 17: $col = 'Q'; break;
                    case 18: $col = 'R'; break;
                    case 19: $col = 'S'; break;
                    case 20: $col = 'T'; break;
                    case 21: $col = 'U'; break;
                    case 22: $col = 'V'; break;
                    case 23: $col = 'W'; break;
                    case 24: $col = 'X'; break;
                    case 25: $col = 'Y'; break;
                    case 26: $col = 'Z'; break;
                }

                $objSpreadsheet->getActiveSheet()->setCellValue($col . $rowCount, $dataProperty);
                $colCount++;
            }
        }

        foreach (range('A', $objSpreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objSpreadsheet
                ->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }
        $objSpreadsheetWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
        $stream = new CallbackStream(function () use ($objSpreadsheetWriter) {
            $objSpreadsheetWriter->save('php://output');
        });

        return $this->response
            ->withType('xlsx')
            ->withHeader('Content-Disposition', 'attachment;filename="' . strtolower($this->defaultTable) . '.' . 'xlsx"')
            ->withBody($stream);
    }

    /**
     * Export csv method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportCsv()
    {
        $users = $this->Users->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->Users->tableColumns;
        $extract = [
            'id',
            'role_id',
            'locale_id',
            'foreign_key',
            'username',
            'name',
            'email',
            function ($row) {
                return ($row['status'] == 1)? 1: 0;
            },
            function ($row) {
                return empty($row['activation_date'])? NULL: $row['activation_date']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['last_login'])? NULL: $row['last_login']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('users'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'users',
                'delimiter' => $delimiter,
                'enclosure' => $enclosure,
                'header'    => $header,
                'extract'   => $extract,
            ]);
    }

    /**
     * Export xml method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportXml()
    {
        $users = $this->Users->find('all');

        $usersArray = [];
        foreach($users as $user) {
            $userArray = [];
            $userArray['id'] = $user->id;
            $userArray['role_id'] = $user->role_id;
            $userArray['locale_id'] = $user->locale_id;
            $userArray['foreign_key'] = $user->foreign_key;
            $userArray['username'] = $user->username;
            $userArray['name'] = $user->name;
            $userArray['email'] = $user->email;
            $userArray['status'] = ($user->status == 1)? 1: 0;
            $userArray['activation_date'] = empty($user->activation_date)? NULL: $user->activation_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userArray['last_login'] = empty($user->last_login)? NULL: $user->last_login->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userArray['created'] = empty($user->created)? NULL: $user->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userArray['modified'] = empty($user->modified)? NULL: $user->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $usersArray[] = $userArray;
        }
        $users = ['Users' => ['User' => $usersArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('users'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'users']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $users = $this->Users->find('all');

        $usersArray = [];
        foreach($users as $user) {
            $userArray = [];
            $userArray['id'] = $user->id;
            $userArray['role_id'] = $user->role_id;
            $userArray['locale_id'] = $user->locale_id;
            $userArray['foreign_key'] = $user->foreign_key;
            $userArray['username'] = $user->username;
            $userArray['name'] = $user->name;
            $userArray['email'] = $user->email;
            $userArray['status'] = ($user->status == 1)? 1: 0;
            $userArray['activation_date'] = empty($user->activation_date)? NULL: $user->activation_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userArray['last_login'] = empty($user->last_login)? NULL: $user->last_login->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userArray['created'] = empty($user->created)? NULL: $user->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userArray['modified'] = empty($user->modified)? NULL: $user->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $usersArray[] = $userArray;
        }
        $users = ['Users' => ['User' => $usersArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('users'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'users']);
    }
}
