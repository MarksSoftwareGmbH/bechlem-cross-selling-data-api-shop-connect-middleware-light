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
namespace BechlemConnectLight\Model\Table;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Mailer\Mailer;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use ArrayObject;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasOne $UserProfiles
 * @property \Cake\ORM\Association\BelongsTo $Roles
 * @property \Cake\ORM\Association\BelongsTo $Locales
 *
 * @method \BechlemConnectLight\Model\Entity\User get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\User findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{

    /**
     * Initialize a table instance. Called after the constructor.
     *
     * You can use this method to define associations, attach behaviors
     * define validation and do any other initialization logic you need.
     *
     * ```
     *  public function initialize(array $config)
     *  {
     *      $this->belongsTo('Users');
     *      $this->belongsToMany('Tagging.Tags');
     *      $this->setPrimaryKey('something_else');
     *  }
     * ```
     *
     * @param array $config Configuration options passed to the constructor
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

        $this->hasOne('UserProfiles', [
            'className' => 'BechlemConnectLight.UserProfiles',
            'dependent' => false,
        ]);
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'className' => 'BechlemConnectLight.Roles',
        ]);
        $this->belongsTo('Locales', [
            'foreignKey' => 'locale_id',
            'className' => 'BechlemConnectLight.Locales',
        ]);

        // Setup search filter using search manager
        $this->searchManager()
            ->value('role_id', [
                'fields' => ['role_id']
            ])
            ->value('locale_id', [
                'fields' => ['locale_id']
            ])
            ->add('search', 'Search.Like', [
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'fields' => [
                    'foreign_key',
                    'username',
                    'name',
                    'email',
                    'Locales.foreign_key',
                    'Locales.name',
                    'Locales.native',
                    'Locales.code',
                    'Roles.foreign_key',
                    'Roles.title',
                    'Roles.alias',
                ],
            ]);
    }

    /**
     * Default table columns.
     *
     * @var array
     */
    public $tableColumns = [
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
    ];

    /**
     * Returns the default validator object. Subclasses can override this function
     * to add a default validation set to the validator object.
     *
     * @param \Cake\Validation\Validator $validator The validator that can be modified to
     * add some rules to it.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->allowEmptyString('uuid_id');

        $validator
            ->allowEmptyString('foreign_key');

        $validator
            ->requirePresence('username', 'create')
            ->notBlank('username');

        $validator
            ->requirePresence('password', 'create')
            ->notBlank('password');

        $validator
            ->requirePresence('name', 'create')
            ->notBlank('name');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notBlank('email');

        $validator
            ->boolean('status')
            ->requirePresence('status', 'create')
            ->notBlank('status');

        $validator
            ->allowEmptyString('token');

        $validator
            ->dateTime('activation_date')
            ->allowEmptyDateTime('activation_date');

        $validator
            ->dateTime('last_login')
            ->allowEmptyDateTime('last_login');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

        $validator
            ->integer('deleted_by')
            ->allowEmptyString('deleted_by');

        return $validator;
    }

    /**
     * Before marshal listener method.
     *
     * @param EventInterface $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        if (isset($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }
    }

    /**
     * Returns a RulesChecker object after modifying the one that was supplied.
     *
     * Subclasses should override this method in order to initialize the rules to be applied to
     * entities saved by this instance.
     *
     * @param \Cake\Datasource\RulesChecker $rules The rules object to be modified.
     * @return \Cake\Datasource\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['username', 'email']), [
            'message' => __d('bechlem_connect_light', 'This username & email combination has already been used.')
        ]);
        $rules->add($rules->existsIn(['role_id'], 'Roles'));
        $rules->add($rules->existsIn(['locale_id'], 'Locales'));

        return $rules;
    }

    /**
     * Find auth
     *
     * @param SelectQuery $query
     * @param array $options
     * @return SelectQuery
     */
    public function findAuth(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        if (!empty($options['username'])) {
            $query
                ->where(['Users.username' => $options['username']])
                ->contain(['Roles']);
        }
        if (!empty($options['username']) && !empty($options['email'])) {
            $query
                ->where([
                    'Users.username' => $options['username'],
                    'Users.email' => $options['email'],
                ])
                ->contain(['Roles']);
        }

        return $query;
    }

    /**
     * Find with full name method
     *
     * @param SelectQuery $query
     * @param array $options
     * @return SelectQuery
     */
    public function findWithFullName(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        $query->select([
            'id',
            'foreign_key',
            'username',
            'name',
            'email',
            'status',
            'last_login',
            'full_name' => $this->selectQuery()->func()->concat([
                'UserProfiles.first_name' => 'literal',
                'UserProfiles.middle_name' => 'literal',
                'UserProfiles.last_name' => 'literal',
            ]),
            'Roles.title',
            'Locales.name',
        ]);

        return $query;
    }

    /**
     * Find by token method
     *
     * @param SelectQuery $query
     * @param array $options
     * @return SelectQuery
     */
    public function findByToken(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        return $query
            ->where([
                'username' => $options['username'],
                'token' => $options['token'],
            ]);
    }

    /**
     * Reset token method
     *
     * @param $user
     *
     * @return bool
     */
    public function resetToken($user)
    {
        $user->token = Text::uuid();
        $user = $this->save($user);
        if (!$user) {
            return false;
        }

        return true;
    }

    /**
     * Send register confirmation email method
     *
     * @param $user
     * @param string $emailTransport
     * @param string $emailFormat
     * @param string $theme
     *
     * @return bool
     */
    public function sendRegisterConfirmationEmail(
        $user,
        string $emailTransport = 'default',
        string $emailFormat = 'text',
        string $theme = 'BechlemConnectLight'
    )
    {
        // Settings based theme
        if (($theme === 'BechlemConnectLight') &&
            (Configure::read('BechlemConnectLight.settings.frontendTheme') !== 'BechlemConnectLight')) {
            $theme = Configure::read('BechlemConnectLight.settings.frontendTheme');
        }

        $email = new Mailer($emailTransport);
        $email->setTo($user->email, $user->name);
        $email->setSubject(__d('bechlem_connect_light', 'Your registration'));
        $email->setViewVars(['user' => $user]);
        $email->setEmailFormat($emailFormat);
        $email->viewBuilder()->setTemplate('register_confirmation');
        $email->viewBuilder()->setTheme($theme);
        if (!$email->send()) {
            return false;
        }

        return true;
    }

    /**
     * Send reset email method
     *
     * @param $user
     * @param array $resetUrl
     * @param string $emailTransport
     * @param string $emailFormat
     * @param string $theme
     *
     * @return bool
     */
    public function sendResetPasswordEmail(
        $user,
        array $resetUrl,
        string $emailTransport = 'default',
        string $emailFormat = 'text',
        string $theme = 'BechlemConnectLight'
    ) {
        // Settings based theme
        if (($theme === 'BechlemConnectLight') &&
            (Configure::read('BechlemConnectLight.settings.frontendTheme') !== 'BechlemConnectLight')) {
            $theme = Configure::read('BechlemConnectLight.settings.frontendTheme');
        }

        $email = new Mailer($emailTransport);
        $email->setTo($user->email, $user->name);
        $email->setSubject(__d('bechlem_connect_light', 'Your reset password link'));
        $email->setViewVars(['user' => $user, 'resetUrl' => $resetUrl]);
        $email->setEmailFormat($emailFormat);
        $email->viewBuilder()->setTemplate('reset_password');
        $email->viewBuilder()->setTheme($theme);
        if (!$email->send()) {
            return false;
        }

        return true;
    }

    /**
     * Change password from reset method
     *
     * @param $user
     * @param array $data
     *
     * @return bool|\Cake\Datasource\EntityInterface|false
     */
    public function changePasswordFromReset($user, array $data)
    {
        $user = $this->patchEntity($user, $data, [
            'fields' => [
                'password',
                'verify_password',
            ]
        ]);
        if ($user->getErrors()) {
            return $user;
        }
        $user->token = Text::uuid();
        $user = $this->save($user);
        if (!$user) {
            return false;
        }

        return true;
    }
}
