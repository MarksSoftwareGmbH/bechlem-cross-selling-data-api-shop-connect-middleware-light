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
 * Registration Entity
 *
 * @property int $id
 * @property int $registration_type_id
 * @property string|null $uuid_id
 * @property string $billing_name
 * @property string|null $billing_name_addition
 * @property string $billing_legal_form
 * @property string $billing_vat_number
 * @property string|null $billing_salutation
 * @property string $billing_first_name
 * @property string|null $billing_middle_name
 * @property string $billing_last_name
 * @property string $billing_management
 * @property string $billing_email
 * @property string|null $billing_website
 * @property string $billing_telephone
 * @property string|null $billing_mobilephone
 * @property string|null $billing_fax
 * @property string $billing_street
 * @property string|null $billing_street_addition
 * @property string $billing_postcode
 * @property string|null $billing_city
 * @property string|null $billing_country
 * @property string|null $shipping_name
 * @property string|null $shipping_name_addition
 * @property string|null $shipping_management
 * @property string|null $shipping_email
 * @property string|null $shipping_telephone
 * @property string|null $shipping_mobilephone
 * @property string|null $shipping_fax
 * @property string|null $shipping_street
 * @property string|null $shipping_street_addition
 * @property string|null $shipping_postcode
 * @property string|null $shipping_city
 * @property string|null $shipping_country
 * @property string $newsletter_email
 * @property string|null $remark
 * @property string $register_excerpt
 * @property bool $newsletter
 * @property bool $marketing
 * @property bool $terms_conditions
 * @property bool $privacy_policy
 * @property string|null $ip
 * @property \Cake\I18n\Time $created
 * @property int $created_by
 * @property \Cake\I18n\Time $modified
 * @property int $modified_by
 * @property \Cake\I18n\Time $deleted
 * @property int $deleted_by
 *
 * @property \BechlemConnectLight\Model\Entity\RegistrationType $registration_type
 */
class Registration extends Entity
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
        'registration_type' => true,
    ];

    /**
     * List of computed or virtual fields that **should** be included in JSON or array
     * representations of this Entity. If a field is present in both _hidden and _virtual
     * the field will **not** be in the array/JSON versions of the entity.
     *
     * @var string[]
     */
    protected array $_virtual = [
        'full_billing_name',
        'full_shipping_name',
        'full_billing_street',
        'full_shipping_street',
        'full_shipping_street',
        'full_billing_address',
        'full_shipping_address',
    ];

    /**
     * Get full billing name method.
     *
     * @return string
     */
    protected function _getFullBillingName()
    {
        if (
            (isset($this->billing_name) && !empty($this->billing_name)) &&
            (isset($this->billing_name_addition) && !empty($this->billing_name_addition))
        ) {
            return $this->billing_name . ' ' . $this->billing_name_addition;
        }

        return '';
    }

    /**
     * Get full shipping name method.
     *
     * @return string
     */
    protected function _getFullShippingName()
    {
        if (
            (isset($this->shipping_name) && !empty($this->shipping_name)) &&
            (isset($this->shipping_name_addition) && !empty($this->shipping_name_addition))
        ) {
            return $this->shipping_name . ' ' . $this->shipping_name_addition;
        }

        return '';
    }

    /**
     * Get full billing street method.
     *
     * @return string
     */
    protected function _getFullBillingStreet()
    {
        if (
            (isset($this->billing_street) && !empty($this->billing_street)) &&
            (isset($this->billing_street_addition) && !empty($this->billing_street_addition))
        ) {
            return $this->billing_street . ' ' . $this->billing_street_addition;
        }

        return '';
    }

    /**
     * Get full shipping street method.
     *
     * @return string
     */
    protected function _getFullShippingStreet()
    {
        if (
            (isset($this->shipping_street) && !empty($this->shipping_street)) &&
            (isset($this->shipping_street_addition) && !empty($this->shipping_street_addition))
        ) {
            return $this->shipping_street . ' ' . $this->shipping_street_addition;
        }

        return '';
    }

    /**
     * Get full billing address method.
     *
     * @return string
     */
    protected function _getFullBillingAddress()
    {
        if (
            (isset($this->billing_street) && !empty($this->billing_street)) &&
            (isset($this->billing_street_addition) && !empty($this->billing_street_addition)) &&
            (isset($this->billing_postcode) && !empty($this->billing_postcode)) &&
            (isset($this->billing_city) && !empty($this->billing_city))
        ) {
            return $this->billing_street . ' '
                . $this->billing_street_addition . ',' . ' '
                . $this->billing_postcode . ' '
                . $this->billing_city;
        }

        return '';
    }

    /**
     * Get full shipping address method.
     *
     * @return string
     */
    protected function _getFullShippingAddress()
    {
        if (
            (isset($this->shipping_street) && !empty($this->shipping_street)) &&
            (isset($this->shipping_street_addition) && !empty($this->shipping_street_addition)) &&
            (isset($this->shipping_postcode) && !empty($this->shipping_postcode)) &&
            (isset($this->shipping_city) && !empty($this->shipping_city))
        ) {
            return $this->shipping_street . ' '
                . $this->shipping_street_addition . ',' . ' '
                . $this->shipping_postcode . ' '
                . $this->shipping_city;
        }

        return '';
    }
}
