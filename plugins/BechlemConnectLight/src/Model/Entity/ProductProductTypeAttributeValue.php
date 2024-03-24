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

/**
 * ProductProductTypeAttributeValue Entity
 *
 * @property int $id
 * @property int|null $product_id
 * @property int|null $product_type_attribute_id
 * @property string|null $value
 * @property \Cake\I18n\DateTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\DateTime|null $modified
 * @property int|null $modified_by
 * @property \Cake\I18n\DateTime|null $deleted
 * @property int|null $deleted_by
 *
 * @property \BechlemConnectLight\Model\Entity\Product $product
 * @property \BechlemConnectLight\Model\Entity\ProductTypeAttribute $product_type_attribute
 */
class ProductProductTypeAttributeValue extends Entity
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
        'product_id' => true,
        'product_type_attribute_id' => true,
        'value' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'deleted' => true,
        'deleted_by' => true,
        'product' => true,
        'product_type_attribute' => true,
    ];
}
