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
namespace BechlemConnectLight\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Product Entity
 *
 * @property int $id
 * @property int|null $product_type_id
 * @property int|null $product_condition_id
 * @property int|null $product_delivery_time_id
 * @property int|null $product_manufacturer_id
 * @property int|null $product_tax_class_id
 * @property string|null $foreign_key
 * @property string|null $employee_key
 * @property string|null $manufacturer_key
 * @property string|null $manufacturer_name
 * @property string|null $manufacturer_sku
 * @property string|null $category_key
 * @property string|null $category_name
 * @property string|null $sku
 * @property string|null $ean
 * @property string|null $name
 * @property string|null $slug
 * @property string $stock
 * @property string $price
 * @property \Cake\I18n\DateTime|null $promote_start
 * @property \Cake\I18n\DateTime|null $promote_end
 * @property bool $promote
 * @property int $promote_position
 * @property \Cake\I18n\DateTime|null $promote_new_start
 * @property \Cake\I18n\DateTime|null $promote_new_end
 * @property bool $promote_new
 * @property int $promote_new_position
 * @property bool $status
 * @property int $view_counter
 * @property \Cake\I18n\DateTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\DateTime|null $modified
 * @property int|null $modified_by
 * @property \Cake\I18n\DateTime|null $deleted
 * @property int|null $deleted_by
 *
 * @property \BechlemConnectLight\Model\Entity\ProductType $product_type
 * @property \BechlemConnectLight\Model\Entity\ProductCondition $product_condition
 * @property \BechlemConnectLight\Model\Entity\ProductDeliveryTime $product_delivery_time
 * @property \BechlemConnectLight\Model\Entity\ProductManufacturer $product_manufacturer
 * @property \BechlemConnectLight\Model\Entity\ProductTaxClass $product_tax_class
 * @property \BechlemConnectLight\Model\Entity\ProductProductTypeAttributeValue[] $product_product_type_attribute_values
 * @property \BechlemConnectLight\Model\Entity\ProductBrand[] $product_brands
 * @property \BechlemConnectLight\Model\Entity\ProductCategory[] $product_categories
 */
class Product extends Entity
{
    
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        'product_type_id' => true,
        'product_condition_id' => true,
        'product_delivery_time_id' => true,
        'product_manufacturer_id' => true,
        'product_tax_class_id' => true,
        'foreign_key' => true,
        'employee_key' => true,
        'manufacturer_key' => true,
        'manufacturer_name' => true,
        'manufacturer_sku' => true,
        'category_key' => true,
        'category_name' => true,
        'sku' => true,
        'ean' => true,
        'name' => true,
        'slug' => true,
        'stock' => true,
        'price' => true,
        'promote_start' => true,
        'promote_end' => true,
        'promote' => true,
        'promote_position' => true,
        'promote_new_start' => true,
        'promote_new_end' => true,
        'promote_new' => true,
        'promote_new_position' => true,
        'status' => true,
        'view_counter' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'deleted' => true,
        'deleted_by' => true,
        'product_type' => true,
        'product_condition' => true,
        'product_delivery_time' => true,
        'product_manufacturer' => true,
        'product_tax_class' => true,
        'product_product_type_attribute_values' => true,
        'product_brands' => true,
        'product_categories' => true,
    ];

    /**
     * Use entity constructor to set the product_product_type_attribute_values as properties of the product entity
     *
     * @param array $properties
     * @param array $options
     */
    public function __construct(array $properties = [], array $options = [])
    {
        if (
            !array_key_exists('product_type', $properties) &&
            array_key_exists('product_type_id', $properties)
        ) {
            $properties['product_type'] = $this->getProductTypeAttributes($properties['product_type_id']);
        }

        // Set the product_type_attributes as keys for the product and product_product_type_attribute_values
        if (array_key_exists('product_type', $properties)) {
            foreach ($properties['product_type']->product_type_attributes as $product_type_attribute) {
                $properties[$product_type_attribute->alias] = null;
            }
        }
        // unset($properties['product_type']->productTypeAttributes);

        // Set the product_product_type_attribute_values for the product_type_attributes by product_type_attribute title
        if (array_key_exists('product_product_type_attribute_values', $properties)) {
            foreach ($properties['product_product_type_attribute_values'] as $product_product_type_attribute_value) {
                $properties[$product_product_type_attribute_value->product_type_attribute->alias] =
                    $product_product_type_attribute_value->value;
            }
        }
        // unset($properties['product_product_type_attribute_values']);
        // unset($properties['product_type']);
        // unset($properties['_matchingData']);

        parent::__construct($properties, $options);
    }

    /**
     * Get product type attributes method.
     *
     * @param int $productTypeId
     * @return \Cake\Datasource\EntityInterface|mixed
     */
    private function getProductTypeAttributes(int $productTypeId)
    {
        $table = TableRegistry::getTableLocator()->get('BechlemConnectLight.ProductTypes');

        return $table->get($productTypeId, contain: ['ProductTypeAttributes']);
    }
}
