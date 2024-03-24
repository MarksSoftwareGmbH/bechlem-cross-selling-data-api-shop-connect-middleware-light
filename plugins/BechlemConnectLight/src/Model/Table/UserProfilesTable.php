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

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserProfiles Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Countries
 *
 * @method \BechlemConnectLight\Model\Entity\UserProfile get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\UserProfile newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\UserProfile[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\UserProfile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\UserProfile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\UserProfile[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\UserProfile findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserProfilesTable extends Table
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

        $this->setTable('user_profiles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'BechlemConnectLight.Users'
        ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id',
            'className' => 'BechlemConnectLight.Countries'
        ]);

        // Setup search filter using search manager
        $this->searchManager()
            ->add('search', 'Search.Like', [
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'fields' => [
                    'foreign_key',
                    'first_name',
                    'middle_name',
                    'last_name',
                    'website',
                    'company',
                    'street',
                    'postcode',
                    'city',
                    'about_me',
                    'tags',
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
        'user_id',
        'foreign_key',
        'prefix',
        'salutation',
        'suffix',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'birthday',
        'website',
        'telephone',
        'mobilephone',
        'fax',
        'company',
        'street',
        'street_addition',
        'postcode',
        'city',
        'country_id',
        'about_me',
        'tags',
        'timezone',
        'image',
        'view_counter',
        'status',
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
            ->allowEmptyString('prefix');

        $validator
            ->allowEmptyString('salutation');

        $validator
            ->allowEmptyString('suffix');

        $validator
            ->allowEmptyString('first_name');

        $validator
            ->allowEmptyString('middle_name');

        $validator
            ->allowEmptyString('last_name');

        $validator
            ->requirePresence('gender', 'create')
            ->notBlank('gender');

        $validator
            ->date('birthday')
            ->allowEmptyDate('birthday');

        $validator
            ->allowEmptyString('image');

        $validator
            ->allowEmptyString('website');

        $validator
            ->allowEmptyString('telephone');

        $validator
            ->allowEmptyString('mobilephone');

        $validator
            ->allowEmptyString('fax');

        $validator
            ->allowEmptyString('company');

        $validator
            ->allowEmptyString('street');

        $validator
            ->allowEmptyString('street_addition');

        $validator
            ->allowEmptyString('postcode');

        $validator
            ->allowEmptyString('city');

        $validator
            ->allowEmptyString('about_me');

        $validator
            ->allowEmptyString('tags');

        $validator
            ->requirePresence('timezone', 'create')
            ->notBlank('timezone');

        $validator
            ->integer('view_counter')
            ->requirePresence('view_counter', 'create')
            ->notBlank('view_counter');

        $validator
            ->boolean('status')
            ->requirePresence('status', 'create')
            ->notBlank('status');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['country_id'], 'Countries'));

        return $rules;
    }
}
