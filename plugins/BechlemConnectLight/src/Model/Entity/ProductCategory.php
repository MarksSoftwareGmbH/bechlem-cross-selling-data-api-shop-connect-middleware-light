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
use Cake\Utility\Text;

/**
 * ProductCategory Entity
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string|null $foreign_key
 * @property int|null $lft
 * @property int|null $rght
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $background_image
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string $locale
 * @property bool $status
 * @property \Cake\I18n\DateTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\DateTime|null $modified
 * @property int|null $modified_by
 * @property \Cake\I18n\DateTime|null $deleted
 * @property int|null $deleted_by
 *
 * @property \BechlemConnectLight\Model\Entity\ParentProductCategory $parent_product_category
 * @property \BechlemConnectLight\Model\Entity\ChildProductCategory[] $child_product_categories
 * @property \BechlemConnectLight\Model\Entity\Product[] $products
 */
class ProductCategory extends Entity
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
        'parent_id' => true,
        'foreign_key' => true,
        'lft' => true,
        'rght' => true,
        'name' => true,
        'slug' => true,
        'description' => true,
        'background_image' => true,
        'meta_description' => true,
        'meta_keywords' => true,
        'locale' => true,
        'status' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'deleted' => true,
        'deleted_by' => true,
        'parent_product_category' => true,
        'child_product_categories' => true,
        'products' => true,
    ];

    /**
     * List of computed or virtual fields that **should** be included in JSON or array
     * representations of this Entity. If a field is present in both _hidden and _virtual
     * the field will **not** be in the array/JSON versions of the entity.
     *
     * @var string[]
     */
    protected array $_virtual = [
        'name_foreign_key',
        'name_locale',
        'name_slug_locale',
    ];

    /**
     * Returns a string with all spaces converted to dashes (by default), accented
     * characters converted to non-accented characters, and non word characters removed.
     *
     * @param string $string the string you want to slug
     * @param string $replacement will replace keys in map
     * @return string
     * @link http://book.cakephp.org/3.0/en/core-libraries/inflector.html#creating-url-safe-strings
     */
    protected function _setName($name)
    {
        $this->set('slug', Text::slug(strtolower($name)));
        return $name;
    }

    /**
     * Get name foreign_key method.
     *
     * @return string
     */
    protected function _getNameForeignKey()
    {
        if (
            (isset($this->name) && !empty($this->name)) &&
            (isset($this->foreign_key) && !empty($this->foreign_key))
        ) {
            return $this->name . ' ' . '(' . $this->foreign_key . ')';
        }

        return '';
    }

    /**
     * Get name locale method.
     *
     * @return string
     */
    protected function _getNameLocale()
    {
        if (
            (isset($this->name) && !empty($this->name)) &&
            (isset($this->locale) && !empty($this->locale))
        ) {
            return $this->name . ' ' . '(' . $this->locale . ')';
        }

        return '';
    }

    /**
     * Get name slug locale method.
     *
     * @return string
     */
    protected function _getNameSlugLocale()
    {
        if (
            (isset($this->name) && !empty($this->name)) &&
            (isset($this->slug) && !empty($this->slug)) &&
            (isset($this->locale) && !empty($this->locale))
        ) {
            return $this->name . ' ' . '(' . $this->slug . ')' . ' ' . '(' . $this->locale . ')';
        }

        return '';
    }
}
