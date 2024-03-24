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
 * ProductTypesProductTypeAttributes Model
 *
 * @property \BechlemConnectLight\Model\Table\ProductTypesTable&\Cake\ORM\Association\BelongsTo $ProductTypes
 * @property \BechlemConnectLight\Model\Table\ProductTypeAttributesTable&\Cake\ORM\Association\BelongsTo $ProductTypeAttributes
 *
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute newEmptyEntity()
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute newEntity(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductTypesProductTypeAttribute[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ProductTypesProductTypeAttributesTable extends Table
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

        $this->setTable('product_types_product_type_attributes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ProductTypes', [
            'foreignKey' => 'product_type_id',
            'joinType' => 'INNER',
            'className' => 'BechlemConnectLight.ProductTypes',
        ]);
        $this->belongsTo('ProductTypeAttributes', [
            'foreignKey' => 'product_type_attribute_id',
            'joinType' => 'INNER',
            'className' => 'BechlemConnectLight.ProductTypeAttributes',
        ]);
    }

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
            ->nonNegativeInteger('position')
            ->notEmptyString('position');

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
        $rules->add($rules->existsIn(['product_type_id'], 'ProductTypes'), ['errorField' => 'product_type_id']);
        $rules->add($rules->existsIn(['product_type_attribute_id'], 'ProductTypeAttributes'), ['errorField' => 'product_type_attribute_id']);

        return $rules;
    }
}
