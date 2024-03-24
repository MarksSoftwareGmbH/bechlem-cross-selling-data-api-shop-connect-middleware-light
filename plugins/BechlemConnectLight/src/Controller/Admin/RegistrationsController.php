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
namespace BechlemConnectLight\Controller\Admin;

use BechlemConnectLight\Controller\Admin\AppController;
use BechlemConnectLight\Utility\BechlemConnectLight;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Http\CallbackStream;
use Cake\I18n\DateTime;
use Cake\Utility\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->Registrations
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->contain(['RegistrationTypes']);

        $registrationTypes = $this->Registrations->RegistrationTypes
            ->find('list', order: ['RegistrationTypes.title' => 'ASC'], keyField: 'alias', valueField: 'title')
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.beforeIndexRender', $this, [
            'Query' => $query,
            'ResignationTypes' => $registrationTypes,
        ]);

        $this->set('registrations', $this->paginate($query));
        $this->set('registrationTypes', $registrationTypes);
    }

    /**
     * View method
     *
     * @param int|null $id Registration id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $id = null)
    {
        $registration = $this->Registrations
            ->find()
            ->where(['Registrations.id' => $id])
            ->contain(['RegistrationTypes'])
            ->first();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.beforeViewRender', $this, [
            'Registration' => $registration,
        ]);

        $this->set('registration', $registration);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $registration = $this->Registrations->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $registration = $this->Registrations->patchEntity($registration, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.beforeAdd', $this, ['Registration' => $registration]);
            if ($this->Registrations->save($registration)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.onAddSuccess', $this, ['Registration' => $registration]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The registration has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.onAddFailure', $this, ['Registration' => $registration]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The registration could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $registrationTypes = $this->Registrations->RegistrationTypes
            ->find('list', order: ['RegistrationTypes.title' => 'ASC'], keyField: 'id', valueField: 'title')
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.beforeAddRender', $this, [
            'Registration' => $registration,
            'RegistrationTypes' => $registrationTypes,
        ]);

        $this->set(compact(
            'registration',
            'registrationTypes'
        ));
    }

    /**
     * Edit method
     *
     * @param int|null $id Registration id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit(int $id = null)
    {
        $registration = $this->Registrations->get($id, contain: ['RegistrationTypes']);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $registration = $this->Registrations->patchEntity($registration, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.beforeEdit', $this, ['Registration' => $registration]);
            if ($this->Registrations->save($registration)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.onEditSuccess', $this, ['Registration' => $registration]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The registration has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.onEditFailure', $this, ['Registration' => $registration]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The registration could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $registrationTypes = $this->Registrations->RegistrationTypes
            ->find('list', order: ['RegistrationTypes.title' => 'ASC'], keyField: 'id', valueField: 'title')
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.beforeEditRender', $this, [
            'Registration' => $registration,
            'RegistrationTypes' => $registrationTypes,
        ]);

        $this->set(compact(
            'registration',
            'registrationTypes'
        ));
    }

    /**
     * Delete method
     *
     * @param int|null $id Registration id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(int $id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $registration = $this->Registrations->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.beforeDelete', $this, [
            'Registration' => $registration,
        ]);
        if ($this->Registrations->delete($registration)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.onDeleteSuccess', $this, [
                'Registration' => $registration,
            ]);
            $connection = ConnectionManager::get('default');
            if ($connection) {
                $connection->delete($this->Registrations->getTable(), ['id' => $id]);
            }
            $this->Flash->set(
                __d('bechlem_connect_light', 'The registration has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Registrations.onDeleteFailure', $this, [
                'Registration' => $registration,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The registration could not be deleted. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Import method
     *
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function import()
    {
        if ($this->getRequest()->is('post') || !empty($this->getRequest()->getData())) {

            $postData = $this->getRequest()->getData();

            // check if delimiter and enclosure are set
            ($postData['delimiter'] == ''? $del = ';': $del = $postData['delimiter']);
            ($postData['enclosure'] == ''? $encl = '"': $encl = $postData['enclosure']);

            if (in_array($postData['file']->getClientMediaType(), [
                'text/comma-separated-values',
                'text/csv',
                'application/csv',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'text/anytext'
            ])) {
                $targetPath = TMP . $postData['file']->getClientFileName();
                $postData['file']->moveTo($targetPath);

                // Transform the csv cols and rows into a associative array based on the alias and rows
                $registrations = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($registrations) && !empty($registrations)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($registrations[0])) {
                    $headerArray = $this->Registrations->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($registrations[0]));
                    if (!empty($headerArrayDiff)) {
                        $this->Flash->set(
                            __d('bechlem_connect_light', 'The uploaded CSV file is incorrectly structured. Please check the format or use a new CSV file.'),
                            ['element' => 'default', 'params' => ['class' => 'error']]
                        );
                        return $this->redirect(['action' => 'import']);
                    }
                } else {
                    $this->Flash->set(
                        __d('bechlem_connect_light', 'The uploaded CSV file is empty.'),
                        ['element' => 'default', 'params' => ['class' => 'error']]
                    );
                    return $this->redirect(['action' => 'import']);
                }

                // Log request
                if ($postData['log'] == 1) {
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $registrations);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($registrations as $registration) {
                    $dateTime = DateTime::now();
                    $existent = $this->Registrations
                        ->find('all')
                        ->where([
                            'billing_name' => $registration['billing_name'],
                            'billing_email' => $registration['billing_email'],
                        ])
                        ->first();
                    if (empty($existent)) {
                        $entity = $this->Registrations->newEmptyEntity(); // create
                        $registration = $this->Registrations->patchEntity(
                            $entity,
                            Hash::merge(
                                $registration,
                                [
                                    'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->Registrations->save($registration)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->Registrations->get($existent->id); // update
                        $registration = $this->Registrations->patchEntity(
                            $existent,
                            Hash::merge(
                                $registration,
                                ['modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss')]
                            )
                        );
                        if ($this->Registrations->save($registration)) {
                            $u++;
                        }
                    }
                }
                $this->Flash->set(
                    __d(
                        'bechlem_connect_light',
                        'You imported {imported} and updated {updated} records.',
                        ['imported' => $i, 'updated' => $u]
                    ),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );
            } else {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'You can only send files with the csv extension csv. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'warning']]
                );
            }

            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Export xlsx method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportXlsx()
    {
        $registrations = $this->Registrations->find('all');
        $header = $this->Registrations->tableColumns;

        $registrationsArray = [];
        foreach($registrations as $registration) {
            $registrationArray = [];
            $registrationArray['id'] = $registration->id;
            $registrationArray['registration_type_id'] = $registration->registration_type_id;
            $registrationArray['billing_name'] = $registration->billing_name;
            $registrationArray['billing_name_addition'] = $registration->billing_name_addition;
            $registrationArray['billing_legal_form'] = $registration->billing_legal_form;
            $registrationArray['billing_vat_number'] = $registration->billing_vat_number;
            $registrationArray['billing_salutation'] = $registration->billing_salutation;
            $registrationArray['billing_first_name'] = $registration->billing_first_name;
            $registrationArray['billing_middle_name'] = $registration->billing_middle_name;
            $registrationArray['billing_last_name'] = $registration->billing_last_name;
            $registrationArray['billing_management'] = $registration->billing_management; 
            $registrationArray['billing_email'] = $registration->billing_email;
            $registrationArray['billing_website'] = $registration->billing_website;
            $registrationArray['billing_telephone'] = $registration->billing_telephone;
            $registrationArray['billing_mobilephone'] = $registration->billing_mobilephone;
            $registrationArray['billing_fax'] = $registration->billing_fax;
            $registrationArray['billing_street'] = $registration->billing_street;
            $registrationArray['billing_street_addition'] = $registration->billing_street_addition;
            $registrationArray['billing_postcode'] = $registration->billing_postcode;
            $registrationArray['billing_city'] = $registration->billing_city;
            $registrationArray['billing_country'] = $registration->billing_country;
            $registrationArray['shipping_name'] = $registration->shipping_name;
            $registrationArray['shipping_name_addition'] = $registration->shipping_name_addition;
            $registrationArray['shipping_management'] = $registration->shipping_management;
            $registrationArray['shipping_email'] = $registration->shipping_email;
            $registrationArray['shipping_telephone'] = $registration->shipping_telephone;
            $registrationArray['shipping_mobilephone'] = $registration->shipping_mobilephone;
            $registrationArray['shipping_fax'] = $registration->shipping_fax;
            $registrationArray['shipping_street'] = $registration->shipping_street;
            $registrationArray['shipping_street_addition'] = $registration->shipping_street_addition;
            $registrationArray['shipping_postcode'] = $registration->shipping_postcode;
            $registrationArray['shipping_city'] = $registration->shipping_city;
            $registrationArray['shipping_country'] = $registration->shipping_country;
            $registrationArray['newsletter_email'] = $registration->newsletter_email;
            $registrationArray['remark'] = $registration->remark;
            $registrationArray['register_excerpt'] = $registration->register_excerpt;
            $registrationArray['newsletter'] = ($registration->newsletter == 1)? 1: 0;
            $registrationArray['marketing'] = ($registration->marketing == 1)? 1: 0;
            $registrationArray['terms_conditions'] = ($registration->terms_conditions == 1)? 1: 0;
            $registrationArray['privacy_policy'] = ($registration->privacy_policy == 1)? 1: 0;
            $registrationArray['ip'] = $registration->ip;
            $registrationArray['created'] = empty($registration->created)? NULL: $registration->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $registrationArray['modified'] = empty($registration->modified)? NULL: $registration->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $registrationsArray[] = $registrationArray;
        }
        $registrations = $registrationsArray;

        $objSpreadsheet = new Spreadsheet();
        $objSpreadsheet->setActiveSheetIndex(0);

        $rowCount = 1;
        $colCount = 1;
        foreach ($header as $headerAlias) {
            $col = 'A';
            switch ($colCount) {
                case 2: $col = 'B'; break;
                case 3: $col = 'C'; break;
                case 4: $col = 'D'; break;
                case 5: $col = 'E'; break;
                case 6: $col = 'F'; break;
                case 7: $col = 'G'; break;
                case 8: $col = 'H'; break;
                case 9: $col = 'I'; break;
                case 10: $col = 'J'; break;
                case 11: $col = 'K'; break;
                case 12: $col = 'L'; break;
                case 13: $col = 'M'; break;
                case 14: $col = 'N'; break;
                case 15: $col = 'O'; break;
                case 16: $col = 'P'; break;
                case 17: $col = 'Q'; break;
                case 18: $col = 'R'; break;
                case 19: $col = 'S'; break;
                case 20: $col = 'T'; break;
                case 21: $col = 'U'; break;
                case 22: $col = 'V'; break;
                case 23: $col = 'W'; break;
                case 24: $col = 'X'; break;
                case 25: $col = 'Y'; break;
                case 26: $col = 'Z'; break;
                case 27: $col = 'AA'; break;
                case 28: $col = 'AB'; break;
                case 29: $col = 'AC'; break;
                case 30: $col = 'AD'; break;
                case 31: $col = 'AE'; break;
                case 32: $col = 'AF'; break;
                case 33: $col = 'AG'; break;
                case 34: $col = 'AH'; break;
                case 35: $col = 'AI'; break;
                case 36: $col = 'AJ'; break;
                case 37: $col = 'AK'; break;
                case 38: $col = 'AL'; break;
                case 39: $col = 'AM'; break;
                case 40: $col = 'AN'; break;
                case 41: $col = 'AO'; break;
                case 42: $col = 'AP'; break;
                case 43: $col = 'AQ'; break;
                case 44: $col = 'AR'; break;
                case 45: $col = 'AS'; break;
                case 46: $col = 'AT'; break;
                case 47: $col = 'AU'; break;
                case 48: $col = 'AV'; break;
                case 49: $col = 'AW'; break;
                case 50: $col = 'AX'; break;
                case 51: $col = 'AY'; break;
                case 52: $col = 'AZ'; break;
            }

            $objSpreadsheet->getActiveSheet()->setCellValue($col . $rowCount, $headerAlias);
            $colCount++;
        }

        $rowCount = 1;
        foreach ($registrations as $dataEntity) {
            $rowCount++;

            $colCount = 1;
            foreach ($dataEntity as $dataProperty) {
                $col = 'A';
                switch ($colCount) {
                    case 2: $col = 'B'; break;
                    case 3: $col = 'C'; break;
                    case 4: $col = 'D'; break;
                    case 5: $col = 'E'; break;
                    case 6: $col = 'F'; break;
                    case 7: $col = 'G'; break;
                    case 8: $col = 'H'; break;
                    case 9: $col = 'I'; break;
                    case 10: $col = 'J'; break;
                    case 11: $col = 'K'; break;
                    case 12: $col = 'L'; break;
                    case 13: $col = 'M'; break;
                    case 14: $col = 'N'; break;
                    case 15: $col = 'O'; break;
                    case 16: $col = 'P'; break;
                    case 17: $col = 'Q'; break;
                    case 18: $col = 'R'; break;
                    case 19: $col = 'S'; break;
                    case 20: $col = 'T'; break;
                    case 21: $col = 'U'; break;
                    case 22: $col = 'V'; break;
                    case 23: $col = 'W'; break;
                    case 24: $col = 'X'; break;
                    case 25: $col = 'Y'; break;
                    case 26: $col = 'Z'; break;
                    case 27: $col = 'AA'; break;
                    case 28: $col = 'AB'; break;
                    case 29: $col = 'AC'; break;
                    case 30: $col = 'AD'; break;
                    case 31: $col = 'AE'; break;
                    case 32: $col = 'AF'; break;
                    case 33: $col = 'AG'; break;
                    case 34: $col = 'AH'; break;
                    case 35: $col = 'AI'; break;
                    case 36: $col = 'AJ'; break;
                    case 37: $col = 'AK'; break;
                    case 38: $col = 'AL'; break;
                    case 39: $col = 'AM'; break;
                    case 40: $col = 'AN'; break;
                    case 41: $col = 'AO'; break;
                    case 42: $col = 'AP'; break;
                    case 43: $col = 'AQ'; break;
                    case 44: $col = 'AR'; break;
                    case 45: $col = 'AS'; break;
                    case 46: $col = 'AT'; break;
                    case 47: $col = 'AU'; break;
                    case 48: $col = 'AV'; break;
                    case 49: $col = 'AW'; break;
                    case 50: $col = 'AX'; break;
                    case 51: $col = 'AY'; break;
                    case 52: $col = 'AZ'; break;
                }

                $objSpreadsheet->getActiveSheet()->setCellValue($col . $rowCount, $dataProperty);
                $colCount++;
            }
        }

        foreach (range('A', $objSpreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objSpreadsheet
                ->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }
        $objSpreadsheetWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
        $stream = new CallbackStream(function () use ($objSpreadsheetWriter) {
            $objSpreadsheetWriter->save('php://output');
        });

        return $this->response
            ->withType('xlsx')
            ->withHeader('Content-Disposition', 'attachment;filename="' . strtolower($this->defaultTable) . '.' . 'xlsx"')
            ->withBody($stream);
    }

    /**
     * Export csv method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportCsv()
    {
        $registrations = $this->Registrations->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->Registrations->tableColumns;
        $extract = [
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
            function ($row) {
                return ($row['newsletter'] == 1)? 1: 0;
            },
            function ($row) {
                return ($row['marketing'] == 1)? 1: 0;
            },
            function ($row) {
                return ($row['terms_conditions'] == 1)? 1: 0;
            },
            function ($row) {
                return ($row['privacy_policy'] == 1)? 1: 0;
            },
            'ip',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('registrations'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'registrations',
                'delimiter' => $delimiter,
                'enclosure' => $enclosure,
                'header'    => $header,
                'extract'   => $extract,
            ]);
    }

    /**
     * Export xml method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportXml()
    {
        $registrations = $this->Registrations->find('all');

        $registrationsArray = [];
        foreach($registrations as $registration) {
            $registrationArray = [];
            $registrationArray['id'] = $registration->id;
            $registrationArray['registration_type_id'] = $registration->registration_type_id;
            $registrationArray['billing_name'] = $registration->billing_name;
            $registrationArray['billing_name_addition'] = $registration->billing_name_addition;
            $registrationArray['billing_legal_form'] = $registration->billing_legal_form;
            $registrationArray['billing_vat_number'] = $registration->billing_vat_number;
            $registrationArray['billing_salutation'] = $registration->billing_salutation;
            $registrationArray['billing_first_name'] = $registration->billing_first_name;
            $registrationArray['billing_middle_name'] = $registration->billing_middle_name;
            $registrationArray['billing_last_name'] = $registration->billing_last_name;
            $registrationArray['billing_management'] = $registration->billing_management; 
            $registrationArray['billing_email'] = $registration->billing_email;
            $registrationArray['billing_website'] = $registration->billing_website;
            $registrationArray['billing_telephone'] = $registration->billing_telephone;
            $registrationArray['billing_mobilephone'] = $registration->billing_mobilephone;
            $registrationArray['billing_fax'] = $registration->billing_fax;
            $registrationArray['billing_street'] = $registration->billing_street;
            $registrationArray['billing_street_addition'] = $registration->billing_street_addition;
            $registrationArray['billing_postcode'] = $registration->billing_postcode;
            $registrationArray['billing_city'] = $registration->billing_city;
            $registrationArray['billing_country'] = $registration->billing_country;
            $registrationArray['shipping_name'] = $registration->shipping_name;
            $registrationArray['shipping_name_addition'] = $registration->shipping_name_addition;
            $registrationArray['shipping_management'] = $registration->shipping_management;
            $registrationArray['shipping_email'] = $registration->shipping_email;
            $registrationArray['shipping_telephone'] = $registration->shipping_telephone;
            $registrationArray['shipping_mobilephone'] = $registration->shipping_mobilephone;
            $registrationArray['shipping_fax'] = $registration->shipping_fax;
            $registrationArray['shipping_street'] = $registration->shipping_street;
            $registrationArray['shipping_street_addition'] = $registration->shipping_street_addition;
            $registrationArray['shipping_postcode'] = $registration->shipping_postcode;
            $registrationArray['shipping_city'] = $registration->shipping_city;
            $registrationArray['shipping_country'] = $registration->shipping_country;
            $registrationArray['newsletter_email'] = $registration->newsletter_email;
            $registrationArray['remark'] = $registration->remark;
            $registrationArray['register_excerpt'] = $registration->register_excerpt;
            $registrationArray['newsletter'] = ($registration->newsletter == 1)? 1: 0;
            $registrationArray['marketing'] = ($registration->marketing == 1)? 1: 0;
            $registrationArray['terms_conditions'] = ($registration->terms_conditions == 1)? 1: 0;
            $registrationArray['privacy_policy'] = ($registration->privacy_policy == 1)? 1: 0;
            $registrationArray['ip'] = $registration->ip;
            $registrationArray['created'] = empty($registration->created)? NULL: $registration->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $registrationArray['modified'] = empty($registration->modified)? NULL: $registration->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $registrationsArray[] = $registrationArray;
        }
        $registrations = ['Registrations' => ['Registration' => $registrationsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('registrations'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'registrations']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $registrations = $this->Registrations->find('all');

        $registrationsArray = [];
        foreach($registrations as $registration) {
            $registrationArray = [];
            $registrationArray['id'] = $registration->id;
            $registrationArray['registration_type_id'] = $registration->registration_type_id;
            $registrationArray['billing_name'] = $registration->billing_name;
            $registrationArray['billing_name_addition'] = $registration->billing_name_addition;
            $registrationArray['billing_legal_form'] = $registration->billing_legal_form;
            $registrationArray['billing_vat_number'] = $registration->billing_vat_number;
            $registrationArray['billing_salutation'] = $registration->billing_salutation;
            $registrationArray['billing_first_name'] = $registration->billing_first_name;
            $registrationArray['billing_middle_name'] = $registration->billing_middle_name;
            $registrationArray['billing_last_name'] = $registration->billing_last_name;
            $registrationArray['billing_management'] = $registration->billing_management; 
            $registrationArray['billing_email'] = $registration->billing_email;
            $registrationArray['billing_website'] = $registration->billing_website;
            $registrationArray['billing_telephone'] = $registration->billing_telephone;
            $registrationArray['billing_mobilephone'] = $registration->billing_mobilephone;
            $registrationArray['billing_fax'] = $registration->billing_fax;
            $registrationArray['billing_street'] = $registration->billing_street;
            $registrationArray['billing_street_addition'] = $registration->billing_street_addition;
            $registrationArray['billing_postcode'] = $registration->billing_postcode;
            $registrationArray['billing_city'] = $registration->billing_city;
            $registrationArray['billing_country'] = $registration->billing_country;
            $registrationArray['shipping_name'] = $registration->shipping_name;
            $registrationArray['shipping_name_addition'] = $registration->shipping_name_addition;
            $registrationArray['shipping_management'] = $registration->shipping_management;
            $registrationArray['shipping_email'] = $registration->shipping_email;
            $registrationArray['shipping_telephone'] = $registration->shipping_telephone;
            $registrationArray['shipping_mobilephone'] = $registration->shipping_mobilephone;
            $registrationArray['shipping_fax'] = $registration->shipping_fax;
            $registrationArray['shipping_street'] = $registration->shipping_street;
            $registrationArray['shipping_street_addition'] = $registration->shipping_street_addition;
            $registrationArray['shipping_postcode'] = $registration->shipping_postcode;
            $registrationArray['shipping_city'] = $registration->shipping_city;
            $registrationArray['shipping_country'] = $registration->shipping_country;
            $registrationArray['newsletter_email'] = $registration->newsletter_email;
            $registrationArray['remark'] = $registration->remark;
            $registrationArray['register_excerpt'] = $registration->register_excerpt;
            $registrationArray['newsletter'] = ($registration->newsletter == 1)? 1: 0;
            $registrationArray['marketing'] = ($registration->marketing == 1)? 1: 0;
            $registrationArray['terms_conditions'] = ($registration->terms_conditions == 1)? 1: 0;
            $registrationArray['privacy_policy'] = ($registration->privacy_policy == 1)? 1: 0;
            $registrationArray['ip'] = $registration->ip;
            $registrationArray['created'] = empty($registration->created)? NULL: $registration->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $registrationArray['modified'] = empty($registration->modified)? NULL: $registration->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $registrationsArray[] = $registrationArray;
        }
        $registrations = ['Registrations' => ['Registration' => $registrationsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('registrations'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'registrations']);
    }
}
