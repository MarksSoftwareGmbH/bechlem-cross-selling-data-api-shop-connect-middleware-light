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
namespace BechlemConnectLight\Controller\Api;

use Cake\Event\Event;

/**
 * Class RegistrationsController
 *
 * @package Api\Controller
 */
class RegistrationsController extends AppController
{

    /**
     * Pagination
     *
     * @var array
     */
    public array $paginate = [
        'limit' => 25,
        'maxLimit' => 50,
        'sortableFields' => [
            'id',
            'registration_type_id',
            'billing_name',
            'billing_name_addition',
            'billing_legal_form',
            'billing_vat_number',
            'billing_salutation',
            'billing_first_name',
            'billing_middle_name',
            'billing_last_name',
            'billing_management',
            'billing_email',
            'billing_website',
            'billing_telephone',
            'billing_mobilephone',
            'billing_fax',
            'billing_street',
            'billing_street_addition',
            'billing_postcode',
            'billing_city',
            'billing_country',
            'shipping_name',
            'shipping_name_addition',
            'shipping_management',
            'shipping_email',
            'shipping_telephone',
            'shipping_mobilephone',
            'shipping_fax',
            'shipping_street',
            'shipping_street_addition',
            'shipping_postcode',
            'shipping_city',
            'shipping_country',
            'newsletter_email',
            'remark',
            'register_excerpt',
            'newsletter',
            'marketing',
            'terms_conditions',
            'privacy_policy',
            'ip',
            'created',
            'modified',
        ],
        'order' => ['created' => 'DESC']
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            if (
                ($this->getRequest()->getQuery('fields') !== null) &&
                !empty($this->getRequest()->getQuery('fields'))
            ) {
                $select = explode(',', $this->getRequest()->getQuery('fields'));
                $this->paginate($event->getSubject()->query->select($select));
            }
            if (
                ($this->getRequest()->getQuery('contain') !== null) &&
                ($this->getRequest()->getQuery('contain') == 1)
            ) {
                $event->getSubject()->query->contain([
                    'RegistrationTypes',
                ]);
            }
        });

        return $this->Crud->execute();
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return mixed
     */
    public function view(int $id = null)
    {
        $this->Crud->on('beforeFind', function (Event $event) {
            if (
                ($this->getRequest()->getQuery('fields') !== null) &&
                !empty($this->getRequest()->getQuery('fields'))
            ) {
                $select = explode(',', $this->getRequest()->getQuery('fields'));
                $event->getSubject()->query->select($select);
            }
            if (
                ($this->getRequest()->getQuery('contain') !== null) &&
                ($this->getRequest()->getQuery('contain') == 1)
            ) {
                $event->getSubject()->query->contain([
                    'RegistrationTypes',
                ]);
            }
        });

        return $this->Crud->execute();
    }
}
