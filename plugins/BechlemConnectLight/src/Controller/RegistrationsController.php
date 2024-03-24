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
namespace BechlemConnectLight\Controller;

use BechlemConnectLight\Controller\AppController;
use Cake\Event\EventInterface;
use BechlemConnectLight\Utility\BechlemConnectLight;

/**
 * Registrations Controller
 *
 * @property \BechlemConnectLight\Model\Table\RegistrationsTable $Registrations
 */
class RegistrationsController extends AppController
{

    /**
     * Locale
     *
     * @var string
     */
    private string $locale;

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
            'RegistrationTypes.title',
        ],
        'order' => ['created' => 'DESC']
    ];

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     * @link https://book.cakephp.org/4/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $session = $this->getRequest()->getSession();
        $this->locale = $session->check('Locale.code')? $session->read('Locale.code'): 'en_US';
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // Get session object
        $session = $this->getRequest()->getSession();

        $registration = $this->Registrations->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $postData = $this->getRequest()->getData();

            $session->write('BechlemConnectLight.Registration', $postData);

            if (
                $session->read('BechlemConnectLight.Captcha.result') !=
                $postData['captcha_result']
            ) {
                return $this->redirect($this->referer());
            }
            unset($postData['captcha_result']);

            if (
                (!isset($postData['shipping_name']) || empty($postData['shipping_name'])) &&
                (!isset($postData['shipping_email']) || empty($postData['shipping_email'])) &&
                (!isset($postData['shipping_telephone']) || empty($postData['shipping_telephone'])) &&
                (!isset($postData['shipping_street']) || empty($postData['shipping_street'])) &&
                (!isset($postData['shipping_postcode']) || empty($postData['shipping_postcode'])) &&
                (!isset($postData['shipping_city']) || empty($postData['shipping_city'])) &&
                (!isset($postData['shipping_country']) || empty($postData['shipping_country']))
            ) {
                $postData['shipping_name']      = $postData['billing_name'];
                $postData['shipping_email']     = $postData['billing_email'];
                $postData['shipping_telephone'] = $postData['billing_telephone'];
                $postData['shipping_street']    = $postData['billing_street'];
                $postData['shipping_postcode']  = $postData['billing_postcode'];
                $postData['shipping_city']      = $postData['billing_city'];
                $postData['shipping_country']   = $postData['billing_country'];
            }

            $registration = $this->Registrations->patchEntity($registration, $postData);
            BechlemConnectLight::dispatchEvent('Controller.Registrations.beforeAdd', $this, ['Registration' => $registration]);
            if ($this->Registrations->save($registration)) {
                BechlemConnectLight::dispatchEvent('Controller.Registrations.onAddSuccess', $this, ['Registration' => $registration]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The registration has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect('/');
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Registrations.onAddFailure', $this, ['Registration' => $registration]);

                $errors = '';
                $validationErrors = $registration->getErrors();
                foreach ($validationErrors as $key => $validationError) {
                    if (is_array($validationError)) {
                        foreach ($validationError as $subKey => $error) {
                            $errors .= $key . ' => ' . $subKey . ' => ' . $error . ' ';
                        }
                    } else {
                        $errors .= $key . ' => ' . $validationError . ' ';
                    }
                }

                $this->Flash->set(
                    __d('bechlem_connect_light', 'The registration could not be saved. Please, try again. We detected following errors: {errors}', ['errors' => $errors]),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );

                return $this->redirect('/');
            }
        }

        $registrationTypes = $this->Registrations->RegistrationTypes
            ->find('list', keyField: 'id', valueField: 'title', order: ['RegistrationTypes.title' => 'ASC'])
            ->toArray();

            BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.beforeAddRender', $this, [
            'Registration' => $registration,
            'RegistrationTypes' => $registrationTypes,
        ]);

        $this->Global->captcha($this);

        $this->set(compact(
            'registration',
            'registrationTypes'
        ));
    }
}
