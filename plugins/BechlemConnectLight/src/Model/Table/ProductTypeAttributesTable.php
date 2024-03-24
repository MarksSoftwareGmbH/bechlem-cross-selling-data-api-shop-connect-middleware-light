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
 * ProductTypeAttributes Model
 *
 * @property \BechlemConnectLight\Model\Table\ProductProductTypeAttributeValuesTable&\Cake\ORM\Association\HasMany $ProductProductTypeAttributeValues
 * @property \BechlemConnectLight\Model\Table\ProductTypeAttributeChoicesTable&\Cake\ORM\Association\HasMany $ProductTypeAttributeChoices
 * @property \BechlemConnectLight\Model\Table\ProductTypesTable&\Cake\ORM\Association\BelongsToMany $ProductTypes
 *
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute newEmptyEntity()
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute newEntity(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypeAttribute[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProductTypeAttributesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('product_type_attributes');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

        $this->hasMany('ProductProductTypeAttributeValues', [
            'foreignKey' => 'product_type_attribute_id',
            'className' => 'BechlemConnectLight.ProductProductTypeAttributeValues',
        ]);
        $this->hasMany('ProductTypeAttributeChoices', [
            'foreignKey' => 'product_type_attribute_id',
            'className' => 'BechlemConnectLight.ProductTypeAttributeChoices',
        ]);
        $this->belongsToMany('ProductTypes', [
            'foreignKey' => 'product_type_attribute_id',
            'targetForeignKey' => 'product_type_id',
            'joinTable' => 'product_types_product_type_attributes',
            'className' => 'BechlemConnectLight.ProductTypes',
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
                    'title',
                    'alias',
                    'type',
                    'description',
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
        'foreign_key',
        'title',
        'alias',
        'type',
        'description',
        'empty_value',
        'wysiwyg',
        'created',
        'modified',
    ];

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('foreign_key')
            ->maxLength('foreign_key', 255)
            ->allowEmptyString('foreign_key');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('alias')
            ->maxLength('alias', 255)
            ->requirePresence('alias', 'create')
            ->notEmptyString('alias');

        $validator
            ->scalar('type')
            ->maxLength('type', 255)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->boolean('empty_value')
            ->notEmptyString('empty_value');

        $validator
            ->boolean('wysiwyg')
            ->requirePresence('wysiwyg', 'create')
            ->allowEmptyString('wysiwyg');

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
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['title']));
        $rules->add($rules->isUnique(['alias']));

        return $rules;
    }
}
