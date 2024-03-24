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
 * ProductCondition Entity
 *
 * @property int $id
 * @property string|null $foreign_key
 * @property string $title
 * @property string $alias
 * @property string|null $description
 * @property \Cake\I18n\DateTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\DateTime|null $modified
 * @property int|null $modified_by
 * @property \Cake\I18n\DateTime|null $deleted
 * @property int|null $deleted_by
 *
 * @property \BechlemConnectLight\Model\Entity\Product[] $products
 */
class ProductCondition extends Entity
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
        'foreign_key' => true,
        'title' => true,
        'alias' => true,
        'description' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'deleted' => true,
        'deleted_by' => true,
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
        'title_foreign_key',
        'title_alias',
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
    protected function _setTitle($title)
    {
        $this->set('alias', Text::slug(strtolower($title)));
        return $title;
    }

    /**
     * Get title foreign_key method.
     *
     * @return string
     */
    protected function _getTitleForeignKey()
    {
        if (
            (isset($this->title) && !empty($this->title)) &&
            (isset($this->foreign_key) && !empty($this->foreign_key))
        ) {
            return $this->title . ' ' . '(' . $this->foreign_key . ')';
        }

        return '';
    }

    /**
     * Get title alias method.
     *
     * @return string
     */
    protected function _getTitleAlias()
    {
        if (
            (isset($this->title) && !empty($this->title)) &&
            (isset($this->alias) && !empty($this->alias))
        ) {
            return $this->title . ' ' . '(' . $this->alias . ')';
        }

        return '';
    }
}
