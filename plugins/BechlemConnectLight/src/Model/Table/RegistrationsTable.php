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
 * Registrations Model
 *
 * @property \BechlemConnectLight\Model\Table\RegistrationTypesTable|\Cake\ORM\Association\BelongsTo $RegistrationTypes
 *
 * @method \BechlemConnectLight\Model\Entity\Registration get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Registration newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Registration[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Registration|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Registration saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Registration patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Registration[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Registration findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RegistrationsTable extends Table
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

        $this->setTable('registrations');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');
        $this->addBehavior('BechlemConnectLight.Datetime');

        $this->belongsTo('RegistrationTypes', [
            'foreignKey' => 'registration_type_id',
            'joinType' => 'INNER',
            'className' => 'BechlemConnectLight.RegistrationTypes',
        ]);

        // Setup search filter using search manager
        $this->searchManager()
            ->value('registration_type', [
                'fields' => ['RegistrationTypes.alias']
            ])
            ->add('search', 'Search.Like', [
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'fields' => [
                    'billing_name',
                    'billing_name_addition',
                    'billing_legal_form',
                    'billing_vat_number',
                    'billing_first_name',
                    'billing_middle_name',
                    'billing_last_name',
                    'billing_management',
                    'billing_email',
                    'billing_website',
                    'billing_telephone',
                    'billing_mobilephone',
                    'billing_fax',
                    'billing_street',
                    'billing_street_addition',
                    'billing_postcode',
                    'billing_city',
                    'billing_country',
                    'shipping_name',
                    'shipping_name_addition',
                    'shipping_management',
                    'shipping_email',
                    'shipping_telephone',
                    'shipping_mobilephone',
                    'shipping_fax',
                    'shipping_street',
                    'shipping_street_addition',
                    'shipping_postcode',
                    'shipping_city',
                    'shipping_country',
                    'newsletter_email',
                    'remark',
                    'ip',
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
        'registration_type_id',
        'billing_name',
        'billing_name_addition',
        'billing_legal_form',
        'billing_vat_number',
        'billing_salutation',
        'billing_first_name',
        'billing_middle_name',
        'billing_last_name',
        'billing_management',
        'billing_email',
        'billing_website',
        'billing_telephone',
        'billing_mobilephone',
        'billing_fax',
        'billing_street',
        'billing_street_addition',
        'billing_postcode',
        'billing_city',
        'billing_country',
        'shipping_name',
        'shipping_name_addition',
        'shipping_management',
        'shipping_email',
        'shipping_telephone',
        'shipping_mobilephone',
        'shipping_fax',
        'shipping_street',
        'shipping_street_addition',
        'shipping_postcode',
        'shipping_city',
        'shipping_country',
        'newsletter_email',
        'remark',
        'register_excerpt',
        'newsletter',
        'marketing',
        'terms_conditions',
        'privacy_policy',
        'ip',
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
            ->scalar('billing_name')
            ->maxLength('billing_name', 255)
            ->requirePresence('billing_name', 'create')
            ->allowEmptyString('billing_name', null, false);

        $validator
            ->scalar('billing_name_addition')
            ->maxLength('billing_name_addition', 255)
            ->allowEmptyString('billing_name_addition');

        $validator
            ->scalar('billing_legal_form')
            ->maxLength('billing_legal_form', 255)
            ->allowEmptyString('billing_legal_form');

        $validator
            ->scalar('billing_vat_number')
            ->maxLength('billing_vat_number', 255)
            ->allowEmptyString('billing_vat_number');

        $validator
            ->scalar('billing_salutation')
            ->maxLength('billing_salutation', 255)
            ->requirePresence('billing_salutation', 'create')
            ->allowEmptyString('billing_salutation', null, false);

        $validator
            ->scalar('billing_first_name')
            ->maxLength('billing_first_name', 255)
            ->requirePresence('billing_first_name', 'create')
            ->allowEmptyString('billing_first_name', null, false);

        $validator
            ->scalar('billing_middle_name')
            ->maxLength('billing_middle_name', 255)
            ->allowEmptyString('billing_middle_name');

        $validator
            ->scalar('billing_last_name')
            ->maxLength('billing_last_name', 255)
            ->requirePresence('billing_last_name', 'create')
            ->allowEmptyString('billing_last_name', null, false);

        $validator
            ->scalar('billing_management')
            ->maxLength('billing_management', 255)
            ->allowEmptyString('billing_management');

        $validator
            ->scalar('billing_email')
            ->maxLength('billing_email', 255)
            ->requirePresence('billing_email', 'create')
            ->allowEmptyString('billing_email', null, false);

        $validator
            ->scalar('billing_website')
            ->maxLength('billing_website', 255)
            ->allowEmptyString('billing_website');

        $validator
            ->scalar('billing_telephone')
            ->maxLength('billing_telephone', 255)
            ->requirePresence('billing_telephone', 'create')
            ->allowEmptyString('billing_telephone', null, false);

        $validator
            ->scalar('billing_mobilephone')
            ->maxLength('billing_mobilephone', 255)
            ->allowEmptyString('billing_mobilephone');

        $validator
            ->scalar('billing_fax')
            ->maxLength('billing_fax', 255)
            ->allowEmptyString('billing_fax');

        $validator
            ->scalar('billing_street')
            ->maxLength('billing_street', 255)
            ->requirePresence('billing_street', 'create')
            ->allowEmptyString('billing_street', null, false);

        $validator
            ->scalar('billing_street_addition')
            ->maxLength('billing_street_addition', 255)
            ->allowEmptyString('billing_street_addition');

        $validator
            ->scalar('billing_postcode')
            ->maxLength('billing_postcode', 255)
            ->requirePresence('billing_postcode', 'create')
            ->allowEmptyString('billing_postcode', null, false);

        $validator
            ->scalar('billing_city')
            ->maxLength('billing_city', 255)
            ->requirePresence('billing_city', 'create')
            ->allowEmptyString('billing_city', null, false);

        $validator
            ->scalar('billing_country')
            ->maxLength('billing_country', 255)
            ->requirePresence('billing_country', 'create')
            ->allowEmptyString('billing_country', null, false);

        $validator
            ->scalar('shipping_name')
            ->maxLength('shipping_name', 255)
            ->requirePresence('shipping_name', 'create')
            ->allowEmptyString('shipping_name', null, false);

        $validator
            ->scalar('shipping_name_addition')
            ->maxLength('shipping_name_addition', 255)
            ->allowEmptyString('shipping_name_addition');

        $validator
            ->scalar('shipping_management')
            ->maxLength('shipping_management', 255)
            ->allowEmptyString('shipping_management');

        $validator
            ->scalar('shipping_email')
            ->maxLength('shipping_email', 255)
            ->requirePresence('shipping_email', 'create')
            ->allowEmptyString('shipping_email', null, false);

        $validator
            ->scalar('shipping_telephone')
            ->maxLength('shipping_telephone', 255)
            ->requirePresence('shipping_telephone', 'create')
            ->allowEmptyString('shipping_telephone', null, false);

        $validator
            ->scalar('shipping_mobilephone')
            ->maxLength('shipping_mobilephone', 255)
            ->allowEmptyString('shipping_mobilephone');

        $validator
            ->scalar('shipping_fax')
            ->maxLength('shipping_fax', 255)
            ->allowEmptyString('shipping_fax');

        $validator
            ->scalar('shipping_street')
            ->maxLength('shipping_street', 255)
            ->requirePresence('shipping_street', 'create')
            ->allowEmptyString('shipping_street', null, false);

        $validator
            ->scalar('shipping_street_addition')
            ->maxLength('shipping_street_addition', 255)
            ->allowEmptyString('shipping_street_addition');

        $validator
            ->scalar('shipping_postcode')
            ->maxLength('shipping_postcode', 255)
            ->requirePresence('shipping_postcode', 'create')
            ->allowEmptyString('shipping_postcode', null, false);

        $validator
            ->scalar('shipping_city')
            ->maxLength('shipping_city', 255)
            ->requirePresence('shipping_city', 'create')
            ->allowEmptyString('shipping_city', null, false);

        $validator
            ->scalar('shipping_country')
            ->maxLength('shipping_country', 255)
            ->requirePresence('shipping_country', 'create')
            ->allowEmptyString('shipping_country', null, false);

        $validator
            ->scalar('newsletter_email')
            ->maxLength('newsletter_email', 255)
            ->allowEmptyString('newsletter_email');

        $validator
            ->scalar('remark')
            ->maxLength('remark', 255)
            ->allowEmptyString('remark');

        $validator
            ->scalar('register_excerpt')
            ->maxLength('register_excerpt', 255)
            ->allowEmptyString('register_excerpt');

        $validator
            ->boolean('newsletter')
            ->allowEmptyString('newsletter', null, false);

        $validator
            ->boolean('marketing')
            ->allowEmptyString('marketing', null, false);

        $validator
            ->boolean('terms_conditions')
            ->allowEmptyString('terms_conditions', null, false);

        $validator
            ->boolean('privacy_policy')
            ->allowEmptyString('privacy_policy', null, false);

        $validator
            ->scalar('ip')
            ->maxLength('ip', 255)
            ->requirePresence('ip', 'create')
            ->allowEmptyString('ip', null, false);

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
        $rules->add($rules->existsIn(['registration_type_id'], 'RegistrationTypes'));

        return $rules;
    }
}
