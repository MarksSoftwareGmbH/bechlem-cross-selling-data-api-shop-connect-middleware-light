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
 * Domain Entity.
 *
 * @property int $id
 * @property string|null $uuid_id
 * @property string $scheme
 * @property string $url
 * @property string $name
 * @property string $theme
 * @property \Cake\I18n\Time $created
 * @property int $created_by
 * @property \Cake\I18n\Time $modified
 * @property int $modified_by
 * @property \Cake\I18n\Time $deleted
 * @property int $deleted_by
 * @property \BechlemConnectLight\Model\Entity\Setting[] $settings
 */
class Domain extends Entity
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
        '*' => true,
        'id' => false,
    ];

    /**
     * List of computed or virtual fields that **should** be included in JSON or array
     * representations of this Entity. If a field is present in both _hidden and _virtual
     * the field will **not** be in the array/JSON versions of the entity.
     *
     * @var string[]
     */
    protected array $_virtual = [
        'name_id',
        'name_scheme',
        'name_url',
    ];

    /**
     * Get name id method.
     *
     * @return string
     */
    protected function _getNameId()
    {
        if (
            (isset($this->name) && !empty($this->name)) &&
            (isset($this->id) && !empty($this->id))
        ) {
            return $this->name . ' ' . '(' . $this->id . ')';
        }

        return '';
    }

    /**
     * Get name scheme method.
     *
     * @return string
     */
    protected function _getNameScheme()
    {
        if (
            (isset($this->name) && !empty($this->name)) &&
            (isset($this->scheme) && !empty($this->scheme))
        ) {
            return $this->name . ' ' . '(' . $this->scheme . ')';
        }

        return '';
    }

    /**
     * Get NameUrl method.
     *
     * @return string
     */
    protected function _getNameUrl()
    {
        if (
            (isset($this->name) && !empty($this->name)) &&
            (isset($this->url) && !empty($this->url))
        ) {
            return $this->name . ' ' . '(' . $this->url . ')';
        }

        return '';
    }
}
