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

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Managers Model
 *
 * @property \BechlemConnectLight\Model\Table\RolesTable|\Cake\ORM\Association\BelongsTo $Roles
 * @property \BechlemConnectLight\Model\Table\LocalesTable|\Cake\ORM\Association\BelongsTo $Locales
 *
 * @method \BechlemConnectLight\Model\Entity\Manager get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Manager newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Manager[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Manager|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Manager|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Manager patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Manager[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Manager findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ManagersTable extends Table
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
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER',
            'className' => 'BechlemConnectLight.Roles'
        ]);
        $this->belongsTo('Locales', [
            'foreignKey' => 'locale_id',
            'joinType' => 'INNER',
            'className' => 'BechlemConnectLight.Locales'
        ]);

        // Setup search filter using search manager
        $this->searchManager()
            ->value('role_id', [
                'fields' => ['role_id']
            ])
            ->value('locale_id', [
                'fields' => ['locale_id'],
            ])
            ->add('search', 'Search.Like', [
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'fields' => [
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
}
