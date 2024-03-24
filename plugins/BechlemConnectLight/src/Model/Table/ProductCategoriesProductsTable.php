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
 * ProductCategoriesProducts Model
 *
 * @property \BechlemConnectLight\Model\Table\ProductCategoriesTable&\Cake\ORM\Association\BelongsTo $ProductCategories
 * @property \BechlemConnectLight\Model\Table\ProductsTable&\Cake\ORM\Association\BelongsTo $Products
 *
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct newEmptyEntity()
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct newEntity(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\ProductCategoriesProduct[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ProductCategoriesProductsTable extends Table
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

        $this->setTable('product_categories_products');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ProductCategories', [
            'foreignKey' => 'product_category_id',
            'joinType' => 'INNER',
            'className' => 'BechlemConnectLight.ProductCategories'
        ]);
        $this->belongsTo('Products', [
            'foreignKey' => 'product_id',
            'joinType' => 'INNER',
            'className' => 'BechlemConnectLight.Products'
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
        $rules->add($rules->existsIn(['product_category_id'], 'ProductCategories'), ['errorField' => 'product_category_id']);
        $rules->add($rules->existsIn(['product_id'], 'Products'), ['errorField' => 'product_id']);

        return $rules;
    }
}
