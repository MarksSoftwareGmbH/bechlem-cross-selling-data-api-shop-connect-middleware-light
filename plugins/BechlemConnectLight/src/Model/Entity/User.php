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
use Authentication\PasswordHasher\DefaultPasswordHasher;

/**
 * User Entity.
 *
 * @property int $id
 * @property int $role_id
 * @property int $locale_id
 * @property \BechlemConnectLight\Model\Entity\Locale $locale
 * @property string|null $uuid_id
 * @property string|null $foreign_key
 * @property string $username
 * @property string $password
 * @property string $name
 * @property string $email
 * @property bool $status
 * @property string $token
 * @property \Cake\I18n\Time $activation_date
 * @property \Cake\I18n\Time $last_login
 * @property \Cake\I18n\Time $created
 * @property int $created_by
 * @property \Cake\I18n\Time $modified
 * @property int $modified_by
 * @property \Cake\I18n\Time $deleted
 * @property int $deleted_by
 *
 * @property \BechlemConnectLight\Model\Entity\Role[] $roles
 */
class User extends Entity
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
     * Fields that are excluded from JSON an array versions of the entity.
     *
     * @var array
     */
    protected array $_hidden = [
        'password',
        'token'
    ];

    /**
     * List of computed or virtual fields that **should** be included in JSON or array
     * representations of this Entity. If a field is present in both _hidden and _virtual
     * the field will **not** be in the array/JSON versions of the entity.
     *
     * @var string[]
     */
    protected array $_virtual = [
        'name_username',
        'name_username_email',
    ];

    /**
     * Set password method.
     *
     * @param string $password password that will be set.
     * @return bool|string
     */
    protected function _setPassword(string $password) : ?string
    {
        if (strlen($password) > 0) {
            return $this->_hashPassword($password);
        }
    }

    /**
     * Hash a password using the configured password hasher,
     * use DefaultPasswordHasher if no one was configured.
     *
     * @param string $password password to be hashed
     * @return mixed
     */
    protected function _hashPassword($password)
    {
        return (new DefaultPasswordHasher())->hash($password);
    }

    /**
     * Get name username method.
     *
     * @return string
     */
    protected function _getNameUsername()
    {
        if (
            (isset($this->name) && !empty($this->name)) &&
            (isset($this->username) && !empty($this->username))
        ) {
            return $this->name . ' - ' . $this->username;
        }

        return '';
    }

    /**
     * Get name username email method.
     *
     * @return string
     */
    protected function _getNameUsernameEmail()
    {
        if (
            (isset($this->name) && !empty($this->name)) &&
            (isset($this->username) && !empty($this->username)) &&
            (isset($this->email) && !empty($this->email))
        ) {
            return $this->name . ' - ' . $this->username . ' ' . ' (' . $this->email . ')';
        }

        return '';
    }
}
