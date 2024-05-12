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

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;

/**
 * Products Model
 *
 * @property \BechlemConnectLight\Model\Table\ProductTypesTable&\Cake\ORM\Association\BelongsTo $ProductTypes
 * @property \BechlemConnectLight\Model\Table\ProductConditionsTable&\Cake\ORM\Association\BelongsTo $ProductConditions
 * @property \BechlemConnectLight\Model\Table\ProductDeliveryTimesTable&\Cake\ORM\Association\BelongsTo $ProductDeliveryTimes
 * @property \BechlemConnectLight\Model\Table\ProductManufacturersTable&\Cake\ORM\Association\BelongsTo $ProductManufacturers
 * @property \BechlemConnectLight\Model\Table\ProductTaxClassesTable&\Cake\ORM\Association\BelongsTo $ProductTaxClasses
 * @property \BechlemConnectLight\Model\Table\ProductProductTypeAttributeValuesTable&\Cake\ORM\Association\HasMany $ProductProductTypeAttributeValues
 * @property \BechlemConnectLight\Model\Table\ProductBrandsTable&\Cake\ORM\Association\BelongsToMany $ProductBrands
 * @property \BechlemConnectLight\Model\Table\ProductCategoriesTable&\Cake\ORM\Association\BelongsToMany $ProductCategories
 *
 * @method \BechlemConnectLight\Model\Entity\Product newEmptyEntity()
 * @method \BechlemConnectLight\Model\Entity\Product newEntity(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \BechlemConnectLight\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProductsTable extends Table
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

        $this->setTable('products');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');
        $this->addBehavior('BechlemConnectLight.Datetime');

        $this->belongsTo('ProductTypes', [
            'foreignKey' => 'product_type_id',
            'className' => 'BechlemConnectLight.ProductTypes',
        ]);
        $this->belongsTo('ProductConditions', [
            'foreignKey' => 'product_condition_id',
            'className' => 'BechlemConnectLight.ProductConditions',
        ]);
        $this->belongsTo('ProductDeliveryTimes', [
            'foreignKey' => 'product_delivery_time_id',
            'className' => 'BechlemConnectLight.ProductDeliveryTimes',
        ]);
        $this->belongsTo('ProductManufacturers', [
            'foreignKey' => 'product_manufacturer_id',
            'className' => 'BechlemConnectLight.ProductManufacturers',
        ]);
        $this->belongsTo('ProductTaxClasses', [
            'foreignKey' => 'product_tax_class_id',
            'className' => 'BechlemConnectLight.ProductTaxClasses',
        ]);
        $this->hasMany('ProductProductTypeAttributeValues', [
            'foreignKey' => 'product_id',
            'className' => 'BechlemConnectLight.ProductProductTypeAttributeValues',
        ]);
        $this->belongsToMany('ProductBrands', [
            'foreignKey' => 'product_id',
            'targetForeignKey' => 'product_brand_id',
            'joinTable' => 'product_brands_products',
            'className' => 'BechlemConnectLight.ProductBrands',
        ]);
        $this->belongsToMany('ProductCategories', [
            'foreignKey' => 'product_id',
            'targetForeignKey' => 'product_category_id',
            'joinTable' => 'product_categories_products',
            'className' => 'BechlemConnectLight.ProductCategories',
        ]);

        // Setup search filter using search manager
        $this->searchManager()
            ->value('product_type', [
                'fields' => ['ProductTypes.alias']
            ])
            ->value('product_condition', [
                'fields' => ['ProductConditions.alias'],
            ])
            ->value('product_delivery_time', [
                'fields' => ['ProductDeliveryTimes.alias'],
            ])
            ->value('product_manufacturer', [
                'fields' => ['ProductManufacturers.slug'],
            ])
            ->add('search', 'Search.Like', [
                'multiValue' => true,
                'multiValueSeparator' => ' ',
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'fields' => [
                    'manufacturer_key',
                    'manufacturer_name',
                    'manufacturer_sku',
                    'category_key',
                    'category_name',
                    'sku',
                    'ean',
                    'name',
                    'ProductTypes.title',
                    'ProductTypes.alias',
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
        'product_type_id',
        'product_condition_id',
        'product_delivery_time_id',
        'product_manufacturer_id',
        'product_tax_class_id',
        'foreign_key',
        'employee_key',
        'manufacturer_key',
        'manufacturer_name',
        'manufacturer_sku',
        'category_key',
        'category_name',
        'sku',
        'ean',
        'name',
        'slug',
        'stock',
        'price',
        'promote_start',
        'promote_end',
        'promote',
        'promote_position',
        'promote_new_start',
        'promote_new_end',
        'promote_new',
        'promote_new_position',
        'status',
        'view_counter',
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
            ->scalar('employee_key')
            ->maxLength('employee_key', 255)
            ->allowEmptyString('employee_key');

        $validator
            ->scalar('manufacturer_key')
            ->maxLength('manufacturer_key', 255)
            ->allowEmptyString('manufacturer_key');

        $validator
            ->scalar('manufacturer_name')
            ->maxLength('manufacturer_name', 255)
            ->allowEmptyString('manufacturer_name');

        $validator
            ->scalar('manufacturer_sku')
            ->maxLength('manufacturer_sku', 255)
            ->allowEmptyString('manufacturer_sku');

        $validator
            ->scalar('category_key')
            ->maxLength('category_key', 255)
            ->allowEmptyString('category_key');

        $validator
            ->scalar('category_name')
            ->maxLength('category_name', 255)
            ->allowEmptyString('category_name');

        $validator
            ->scalar('sku')
            ->maxLength('sku', 255)
            ->allowEmptyString('sku');

        $validator
            ->scalar('ean')
            ->maxLength('ean', 255)
            ->allowEmptyString('ean');

        $validator
            ->scalar('name')
            ->allowEmptyString('name');

        $validator
            ->scalar('slug')
            ->allowEmptyString('slug');

        $validator
            ->decimal('stock')
            ->notEmptyString('stock');

        $validator
            ->decimal('price')
            ->notEmptyString('price');

        $validator
            ->dateTime('promote_start')
            ->allowEmptyString('promote_start');

        $validator
            ->dateTime('promote_end')
            ->allowEmptyString('promote_end');

        $validator
            ->boolean('promote')
            ->notEmptyString('promote');

        $validator
            ->nonNegativeInteger('promote_position')
            ->notEmptyString('promote_position');

        $validator
            ->dateTime('promote_new_start')
            ->allowEmptyString('promote_new_start');

        $validator
            ->dateTime('promote_new_end')
            ->allowEmptyString('promote_new_end');

        $validator
            ->boolean('promote_new')
            ->notEmptyString('promote_new');

        $validator
            ->nonNegativeInteger('promote_new_position')
            ->notEmptyString('promote_new_position');

        $validator
            ->boolean('status')
            ->notEmptyString('status');

        $validator
            ->integer('view_counter')
            ->requirePresence('view_counter', 'create')
            ->notBlank('view_counter');

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
        $rules->add($rules->existsIn(['product_type_id'], 'ProductTypes'), ['errorField' => 'product_type_id']);
        $rules->add($rules->existsIn(['product_condition_id'], 'ProductConditions'), ['errorField' => 'product_condition_id']);
        $rules->add($rules->existsIn(['product_delivery_time_id'], 'ProductDeliveryTimes'), ['errorField' => 'product_delivery_time_id']);
        $rules->add($rules->existsIn(['product_manufacturer_id'], 'ProductManufacturers'), ['errorField' => 'product_manufacturer_id']);
        $rules->add($rules->existsIn(['product_tax_class_id'], 'ProductTaxClasses'), ['errorField' => 'product_tax_class_id']);

        return $rules;
    }

    /**
     * Find promoted method.
     *
     * @param SelectQuery $query
     * @param array $options
     * @return SelectQuery
     */
    public function findPromoted(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        $query
            ->contain([
                'ProductTypes'                      => ['ProductTypeAttributes'],
                'ProductProductTypeAttributeValues' => ['ProductTypeAttributes']
            ])
            ->where([
                'Products.promote_start <=' => $options['date'],
                'Products.promote'          => 1,
                'Products.status'           => 1,
            ])
            ->orderBy(['Products.promote_position' => $options['products_order']]);

        return $query;
    }

    /**
     * Find index method.
     *
     * @param SelectQuery $query
     * @param array $options
     * @return SelectQuery
     */
    public function findIndex(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        $query
            ->contain([
                'ProductTypes'                      => ['ProductTypeAttributes'],
                'ProductProductTypeAttributeValues' => ['ProductTypeAttributes']
            ])
            ->matching('ProductTypes', function ($q) use ($options) {
                return $q
                    ->where(['ProductTypes.alias' => $options['product_type']]);
            })
            ->where([
                'Products.promote_start <=' => $options['date'],
                'Products.status' => 1,
            ])
            ->orderBy(['Products.created' => $options['products_order']]);

        return $query;
    }

    /**
     * Find all products method.
     *
     * @param SelectQuery $query
     * @param array $options
     *
     * @return SelectQuery
     */
    public function findAllProducts(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        $query
            ->contain([
                'ProductTypes'                      => ['ProductTypeAttributes'],
                'ProductProductTypeAttributeValues' => ['ProductTypeAttributes']
            ]);

        return $query;
    }

    /**
     * Find by ProductType method.
     *
     * @param SelectQuery $query
     * @param array $options
     * @return SelectQuery
     */
    public function findByProductType(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        $query
            ->contain([
                'ProductTypes'                      => ['ProductTypeAttributes'],
                'ProductProductTypeAttributeValues' => ['ProductTypeAttributes']
            ])
            ->matching('ProductTypes', function ($q) use ($options) {
                return $q
                    ->where(['ProductTypes.alias' => $options['product_type']]);
            });

        // Individual order key
        if (empty($options['order_key'])) {
            $options['order_key'] = 'created';
        }
        // Order direction
        if (empty($options['order_direction'])) {
            $options['order_direction'] = 'ASC';
        }
        $query->orderBy(['Products' . '.' . $options['order_key'] => $options['order_direction']]);

        return $query;
    }

    /**
     * Find by ProductType and Id method.
     *
     * @param SelectQuery $query
     * @param array $options
     * @return SelectQuery
     */
    public function findByProductTypeAndId(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        $query
            ->matching('ProductProductTypeAttributeValues.ProductTypeAttributes.ProductTypes',
                function ($q) use ($options) {
                    $foreignKey = empty($options['data']['id'])? null: $options['data']['id'];
                    return $q
                        ->where([
                            'ProductTypes.alias'                        => $options['product_type'],
                            'ProductTypeAttributes.alias'               => 'foreign_key',
                            'ProductProductTypeAttributeValues.value'   => $foreignKey,
                        ]);
                })
            ->contain([
                'ProductTypes.ProductTypeAttributes',
                'ProductProductTypeAttributeValues.ProductTypeAttributes',
            ]);

        return $query;
    }

    /**
     * Find by slug method.
     *
     * @param SelectQuery $quer
     * @param array $options
     * @return SelectQuery
     */
    public function findBySlug(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        $query
            ->contain([
                'ProductTypes'                      => ['ProductTypeAttributes'],
                'ProductProductTypeAttributeValues' => ['ProductTypeAttributes']
            ])
            ->matching('ProductTypes', function ($q) use ($options) {
                return $q
                    ->where(['ProductTypes.alias' => $options['product_type']]);
            })
            ->where([
                'Products.slug'             => $options['slug'],
                'Products.promote_start <=' => $options['date'],
                'Products.status'           => 1,
            ]);

        return $query;
    }

    /**
     * Find by slug and id method.
     *
     * @param SelectQuery $query
     * @param array $options
     * @return SelectQuery
     */
    public function findBySlugAndId(SelectQuery $query, array $options)
    {
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
        }

        $query
            ->contain([
                'ProductTypes' => ['ProductTypeAttributes'],
                'ProductProductTypeAttributeValues' => ['ProductTypeAttributes']
            ])
            ->where([
                'Products.id'       => $options['id'],
                'Products.slug'     => $options['slug'],
                'Products.status'   => 1,
            ]);

        return $query;
    }

    /**
     * Get type with ProductTypes method.
     *
     * @param string $productType
     *
     * @return object
     */
    public function getTypeWithProductTypes(string $productType = null)
    {
        return $this->ProductTypes
            ->find()
            ->where(['alias' => Text::slug($productType)])
            ->contain('ProductTypeAttributes')
            ->firstOrFail();
    }

    /**
     * Api save method.
     *
     * @param array $data
     *
     * @return bool|mixed
     */
    public function apiSave(array $data)
    {
        $product = $this
            ->find('byProductTypeAndId', options: [
                'data'          => $data,
                'product_type'  => Text::slug($data['product_type'])
            ])
            ->first();

        if (empty($product)) {
            return $this->apiCreate($data);
        }

        return $this->apiUpdate($product, $data);
    }

    /**
     * Api create method.
     *
     * @param array $data
     *
     * @return bool|mixed
     */
    public function apiCreate(array $data)
    {
        // Get ProductType
        $type = $this->getTypeWithProductTypes(Text::slug($data['product_type']));

        // If id attribute given use it as foreign_key
        if (!empty($data['id'])) {
            $data['foreign_key'] = $data['id'];
        }

        $entity                         = $this->newEmptyEntity();
        $entity->product_type           = $type;
        $entity->product_condition      = $this->ProductConditions
            ->find()
            ->where(['id' => $data['product_condition_id']])
            ->firstOrFail();
        $entity->product_delivery_time  = $this->ProductDeliveryTimes
            ->find()
            ->where(['id' => $data['product_delivery_time_id']])
            ->firstOrFail();
        $entity->product_manufacturer   = $this->ProductManufacturers
            ->find()
            ->where(['id' => $data['product_manufacturer_id']])
            ->firstOrFail();
        $entity->product_tax_class      = $this->ProductTaxClasses
            ->find()
            ->where(['id' => $data['product_tax_class_id']])
            ->firstOrFail();
        $entity->foreign_key            = empty($data['foreign_key'])? null: urldecode($data['foreign_key']);
        $entity->employee_key           = empty($data['employee_key'])? null: urldecode($data['employee_key']);
        $entity->manufacturer_key       = empty($data['manufacturer_key'])? null: urldecode($data['manufacturer_key']);
        $entity->manufacturer_name      = empty($data['manufacturer_name'])? null: urldecode($data['manufacturer_name']);
        $entity->manufacturer_sku       = empty($data['manufacturer_sku'])? null: urldecode($data['manufacturer_sku']);
        $entity->category_key           = empty($data['category_key'])? null: urldecode($data['category_key']);
        $entity->category_name          = empty($data['category_name'])? null: urldecode($data['category_name']);
        $entity->sku                    = empty($data['sku'])? null: urldecode($data['sku']);
        $entity->ean                    = empty($data['ean'])? null: urldecode($data['ean']);
        $entity->name                   = empty($data['name'])? null: urldecode($data['name']);
        $entity->slug                   = empty($data['name'])? null: strtolower(Text::slug(trim(urldecode($data['name']))));
        $entity->stock                  = empty($data['stock'])? 0.0000: urldecode($data['stock']);
        $entity->price                  = empty($data['price'])? 0.0000: urldecode($data['price']);
        $entity->promote_start          = empty($data['promote_start'])? null: urldecode($data['promote_start']);
        $entity->promote_end            = empty($data['promote_end'])? null: urldecode($data['promote_end']);
        $entity->promote                = empty($data['promote'])? 0: urldecode($data['promote']);
        $entity->promote_position       = empty($data['promote_position'])? 0: urldecode($data['promote_position']);
        $entity->promote_new_start      = empty($data['promote_new_start'])? null: urldecode($data['promote_new_start']);
        $entity->promote_new_end        = empty($data['promote_new_end'])? null: urldecode($data['promote_new_end']);
        $entity->promote_new            = empty($data['promote_new'])? 0: urldecode($data['promote_new']);
        $entity->promote_new_position   = empty($data['promote_new_position'])? 0: urldecode($data['promote_new_position']);
        $entity->status                 = empty($data['status'])? 0: urldecode($data['status']);
        $entity->product_product_type_attribute_values = [];

        $product = $this->patchProduct($type, $entity, $data);

        if ($this->save($product)) {
            return $product;
        } else {
            return false;
        }
    }

    /**
     * Api update method.
     *
     * @param object $product
     * @param array $data
     *
     * @return bool|mixed
     */
    public function apiUpdate($product, array $data)
    {
        $type = $this->getTypeWithProductTypes($product->product_type->alias);

        $existentProduct = $this->get($product->id);
        $this->patchEntity($existentProduct, ['modified' => date('Y-m-d H:i:s')]);
        if ($this->save($existentProduct)) {
            // Update values
            foreach ($product->product_product_type_attribute_values as $value) {

                $attributeAlias = $value->product_type_attribute->alias;
                $attribute = $this->ProductProductTypeAttributeValues->get($value->id);

                if (is_array(json_decode($value->value, true))) {

                    $comparisonValue = json_decode($value->value, true);
                    array_walk_recursive($comparisonValue, [$this, 'encode']);

                } else {

                    $comparisonValue = urlencode($value->value);

                }

                if (array_key_exists($attributeAlias, $data)) {

                    if ($comparisonValue == $data[$attributeAlias]) {
                        unset($data[$attributeAlias]);
                        continue;
                    }

                    if (!is_array($data[$attributeAlias])) {

                        $newValue['value'] = urldecode($data[$attributeAlias]);
                        $this->ProductProductTypeAttributeValues->patchEntity($attribute, $newValue);

                        if ($this->ProductProductTypeAttributeValues->save($attribute)) {
                            unset($data[$attributeAlias]);
                            continue;
                        }
                    }

                    $newValue['value'] = urldecode(json_encode($data[$attributeAlias]));
                    $this->ProductProductTypeAttributeValues->patchEntity($attribute, $newValue);

                    if ($this->ProductProductTypeAttributeValues->save($attribute)) {
                        unset($data[$attributeAlias]);
                        continue;
                    }

                } else {

                    if ($attributeAlias != 'foreign_key') {
                        $this->ProductProductTypeAttributeValues->delete($value);
                    }

                }
            }

            // Add new values
            if (!empty($data)) {

                foreach ($type->product_type_attributes as $attribute) {

                    // Alias exists in send
                    if (array_key_exists($attribute->alias, $data)) {

                        $attributeAlias = $attribute->alias;
                        $newData        = '';

                        if (isset($data[$attributeAlias])) {
                            $newData = $data[$attributeAlias];
                        }

                        if (is_array($newData)) {
                            $newData = json_encode($newData);
                        }

                        $entity = $this->ProductProductTypeAttributeValues->newEntity([
                            'product_type_attribute_id' => $attribute->id,
                            'product_id'                => $product->id,
                            'value'                     => urldecode($newData)
                        ]);

                        $this->ProductProductTypeAttributeValues->save($entity);
                    }
                }
            }

            return $product;
        } else {
            return false;
        }
    }

    /**
     * Patch product method.
     *
     * @param object $type
     * @param object $product
     * @param array $data
     *
     * @return mixed
     */
    public function patchProduct(object $type, object $product, array $data)
    {
        foreach ($type->product_type_attributes as $attribute) {

            // Condition for default entity creation
            if (isset($data[$attribute->alias])) {

                if (is_array($data[$attribute->alias])) {
                    $data[$attribute->alias] = json_encode($data[$attribute->alias]);
                }

                $entity = $this->ProductProductTypeAttributeValues->newEntity([
                    'value'                     => urldecode($data[$attribute->alias]),
                    'product_type_attribute_id' => $attribute->id
                ]);

                array_push($product->product_product_type_attribute_values, $entity);
            }
        }

        return $product;
    }

    /**
     * Encode method.
     *
     * @param $item
     * @param $key
     *
     * @return void
     */
    public function encode(&$item, $key)
    {
        $item = urlencode($item);
    }
}
