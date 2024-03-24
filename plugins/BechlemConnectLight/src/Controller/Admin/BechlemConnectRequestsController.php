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
use Cake\Event\EventInterface;
use Cake\Http\CallbackStream;
use Cake\I18n\DateTime;
use Cake\Utility\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * BechlemConnectRequests Controller
 *
 * @property \BechlemConnectLight\Model\Table\BechlemConnectRequestsTable $BechlemConnectRequests
 */
class BechlemConnectRequestsController extends AppController
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
            'bechlem_connect_config_id',
            'name',
            'slug',
            'method',
            'url',
            'data',
            'language',
            'description',
            'example',
            'options',
            'log',
            'status',
            'created',
            'modified',
            'BechlemConnectConfigs.title',
        ],
        'order' => ['method' => 'ASC']
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
        $this->locale = $session->check('Locale.code')? $session->read('Locale.code'): 'de_DE';
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $query = $this->BechlemConnectRequests
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->contain(['BechlemConnectConfigs']);

        $bechlemConnectRequestMethods = $this->BechlemConnectRequests
            ->find('list',
                order: ['BechlemConnectRequests.method' => 'ASC'],
                keyField: 'method',
                valueField: 'method'
            )
            ->toArray();

        $bechlemConnectConfigs = $this->BechlemConnectRequests->BechlemConnectConfigs
            ->find('list',
                order: ['BechlemConnectConfigs.title' => 'ASC'],
                keyField: 'alias',
                valueField: 'title'
            )
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.beforeIndexRender', $this, [
            'Query' => $query,
            'BechlemConnectRequestMethods' => $bechlemConnectRequestMethods,
            'BechlemConnectConfigs' => $bechlemConnectConfigs,
        ]);

        $this->set('bechlemConnectRequests', $this->paginate($query));
        $this->set(compact('bechlemConnectRequestMethods', 'bechlemConnectConfigs'));
    }

    /**
     * Run method
     *
     * @param int|null $id Bechlem Connect Request id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function run(int $id = null)
    {
        $bechlemConnectRequest = $this->BechlemConnectRequests->get($id, contain: ['BechlemConnectConfigs']);
        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.beforeRun', $this, [
            'BechlemConnectRequest' => $bechlemConnectRequest
        ]);
        if ($this->BechlemConnectRequests->runRequest($this, $bechlemConnectRequest)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.onRunSuccess', $this, [
                'BechlemConnectRequest' => $bechlemConnectRequest
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The Bechlem connect request has been executed.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.onRunFailure', $this, [
                'BechlemConnectRequest' => $bechlemConnectRequest
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The Bechlem connect request could not be executed. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect($this->referer());
    }

    /**
     * View method
     *
     * @param int|null $id Bechlem Connect Request id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $id = null)
    {
        $bechlemConnectRequest = $this->BechlemConnectRequests->get($id, contain: ['BechlemConnectConfigs']);

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.beforeViewRender', $this, [
            'BechlemConnectRequest' => $bechlemConnectRequest
        ]);

        $this->set('bechlemConnectRequest', $bechlemConnectRequest);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $bechlemConnectRequest = $this->BechlemConnectRequests->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $bechlemConnectRequest = $this->BechlemConnectRequests->patchEntity(
                $bechlemConnectRequest,
                $this->getRequest()->getData()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.beforeAdd', $this, [
                'BechlemConnectRequest' => $bechlemConnectRequest
            ]);
            if ($this->BechlemConnectRequests->save($bechlemConnectRequest)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.onAddSuccess', $this, [
                    'BechlemConnectRequest' => $bechlemConnectRequest
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem connect request has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.onAddFailure', $this, [
                    'BechlemConnectRequest' => $bechlemConnectRequest
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem connect request could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $bechlemConnectConfigs = $this->BechlemConnectRequests->BechlemConnectConfigs
            ->find('list',
                order: ['BechlemConnectConfigs.title' => 'ASC'],
                keyField: 'id',
                valueField: 'title'
            )
            ->where(['BechlemConnectConfigs.status' => 1]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.beforeAddRender', $this, [
            'BechlemConnectRequest' => $bechlemConnectRequest,
            'BechlemConnectConfigs' => $bechlemConnectConfigs,
        ]);

        $this->set(compact('bechlemConnectRequest', 'bechlemConnectConfigs'));
    }

    /**
     * Edit method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function edit(int $id = null)
    {
        $bechlemConnectRequest = $this->BechlemConnectRequests->get($id, contain: ['BechlemConnectConfigs']);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $bechlemConnectRequest = $this->BechlemConnectRequests->patchEntity(
                $bechlemConnectRequest,
                $this->getRequest()->getData()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.beforeEdit', $this, [
                'BechlemConnectRequest' => $bechlemConnectRequest
            ]);
            if ($this->BechlemConnectRequests->save($bechlemConnectRequest)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.onEditSuccess', $this, [
                    'BechlemConnectRequest' => $bechlemConnectRequest
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem connect request has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.onEditFailure', $this, [
                    'BechlemConnectRequest' => $bechlemConnectRequest
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem connect request could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $bechlemConnectConfigs = $this->BechlemConnectRequests->BechlemConnectConfigs
            ->find('list',
                order: ['BechlemConnectConfigs.title' => 'ASC'],
                keyField: 'id',
                valueField: 'title'
            )
            ->where(['BechlemConnectConfigs.status' => 1]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.beforeEditRender', $this, [
            'BechlemConnectRequest' => $bechlemConnectRequest,
            'BechlemConnectConfigs' => $bechlemConnectConfigs,
        ]);

        $this->set(compact('bechlemConnectRequest', 'bechlemConnectConfigs'));
    }

    /**
     * Copy method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function copy(int $id = null)
    {
        $this->getRequest()->allowMethod(['post']);

        $bechlemConnectRequest = $this->BechlemConnectRequests->get($id, contain: ['BechlemConnectConfigs']);
        $bechlemConnectRequest->setNew(true);
        $bechlemConnectRequest->unset('id');
        $bechlemConnectRequest->name = $bechlemConnectRequest->name . ' ' . '(' . __d('bechlem_connect_light', 'Copy') . ')';
        $bechlemConnectRequest->log = 0;
        $bechlemConnectRequest->status = 0;

        if ($this->BechlemConnectRequests->save($bechlemConnectRequest)) {
            $this->Flash->set(
                __d('bechlem_connect_light', 'The Bechlem connect request has been copied.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );

            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->set(
                __d('bechlem_connect_light', 'The Bechlem connect request could not be copied. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Delete method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function delete(int $id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $bechlemConnectRequest = $this->BechlemConnectRequests->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.beforeDelete', $this, [
            'BechlemConnectRequest' => $bechlemConnectRequest
        ]);
        if ($this->BechlemConnectRequests->delete($bechlemConnectRequest)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.onDeleteSuccess', $this, [
                'BechlemConnectRequest' => $bechlemConnectRequest
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The Bechlem connect request has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]);
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemConnectRequests.onDeleteFailure', $this, [
                'BechlemConnectRequest' => $bechlemConnectRequest
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The Bechlem connect request could not be deleted. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Import method
     *
     * @return \Cake\Http\Response|void|null
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
                $bechlemConnectRequests = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($bechlemConnectRequests) && !empty($bechlemConnectRequests)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($bechlemConnectRequests[0])) {
                    $headerArray = $this->BechlemConnectRequests->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($bechlemConnectRequests[0]));
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
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $bechlemConnectRequests);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($bechlemConnectRequests as $request) {
                    $dateTime = DateTime::now();
                    $existent = $this->BechlemConnectRequests
                        ->find('all')
                        ->where([
                            'bechlem_connect_config_id' => $request['bechlem_connect_config_id'],
                            'name' => $request['name'],
                            'slug' => $request['slug'],
                            'method' => $request['method'],
                            'url' => $request['url'],
                        ])
                        ->first();

                    if (empty($existent)) {
                        $entity = $this->BechlemConnectRequests->newEmptyEntity(); // create
                        $request = $this->BechlemConnectRequests->patchEntity(
                            $entity,
                            Hash::merge(
                                $request,
                                [
                                    'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->BechlemConnectRequests->save($request)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->BechlemConnectRequests->get($existent->id); // update
                        $request = $this->BechlemConnectRequests->patchEntity(
                            $existent,
                            Hash::merge(
                                $request,
                                ['modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss')]
                            )
                        );
                        if ($this->BechlemConnectRequests->save($request)) {
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
        $bechlemConnectRequests = $this->BechlemConnectRequests->find('all');
        $header = $this->BechlemConnectRequests->tableColumns;

        $bechlemConnectRequestsArray = [];
        foreach($bechlemConnectRequests as $bechlemConnectRequest) {
            $bechlemConnectRequestArray = [];
            $bechlemConnectRequestArray['id'] = $bechlemConnectRequest->id;
            $bechlemConnectRequestArray['bechlem_connect_config_id'] = $bechlemConnectRequest->bechlem_connect_config_id;
            $bechlemConnectRequestArray['name'] = $bechlemConnectRequest->name;
            $bechlemConnectRequestArray['slug'] = $bechlemConnectRequest->slug;
            $bechlemConnectRequestArray['method'] = $bechlemConnectRequest->method;
            $bechlemConnectRequestArray['url'] = $bechlemConnectRequest->url;
            $bechlemConnectRequestArray['data'] = $bechlemConnectRequest->data;
            $bechlemConnectRequestArray['language'] = $bechlemConnectRequest->language;
            $bechlemConnectRequestArray['description'] = $bechlemConnectRequest->description;
            $bechlemConnectRequestArray['example'] = $bechlemConnectRequest->example;
            $bechlemConnectRequestArray['options'] = $bechlemConnectRequest->options;
            $bechlemConnectRequestArray['log'] = ($bechlemConnectRequest->log == 1)? 1: 0;
            $bechlemConnectRequestArray['status'] = ($bechlemConnectRequest->status == 1)? 1: 0;
            $bechlemConnectRequestArray['created'] = empty($bechlemConnectRequest->created)? NULL: $bechlemConnectRequest->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemConnectRequestArray['modified'] = empty($bechlemConnectRequest->modified)? NULL: $bechlemConnectRequest->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $bechlemConnectRequestsArray[] = $bechlemConnectRequestArray;
        }
        $bechlemConnectRequests = $bechlemConnectRequestsArray;

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
            }

            $objSpreadsheet->getActiveSheet()->setCellValue($col . $rowCount, $headerAlias);
            $colCount++;
        }

        $rowCount = 1;
        foreach ($bechlemConnectRequests as $dataEntity) {
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
        $bechlemConnectRequests = $this->BechlemConnectRequests->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->BechlemConnectRequests->tableColumns;
        $extract = [
            'id',
            'bechlem_connect_config_id',
            'name',
            'slug',
            'method',
            'url',
            'language',
            'data',
            'options',
            'description',
            'example',
            function ($row) {
                return ($row['log'] == 1)? 1: 0;
            },
            function ($row) {
                return ($row['status'] == 1)? 1: 0;
            },
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('bechlemConnectRequests'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'bechlemConnectRequests',
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
        $bechlemConnectRequests = $this->BechlemConnectRequests->find('all');

        $bechlemConnectRequestsArray = [];
        foreach($bechlemConnectRequests as $bechlemConnectRequest) {
            $bechlemConnectRequestArray = [];
            $bechlemConnectRequestArray['id'] = $bechlemConnectRequest->id;
            $bechlemConnectRequestArray['bechlem_connect_config_id'] = $bechlemConnectRequest->bechlem_connect_config_id;
            $bechlemConnectRequestArray['name'] = $bechlemConnectRequest->name;
            $bechlemConnectRequestArray['slug'] = $bechlemConnectRequest->slug;
            $bechlemConnectRequestArray['method'] = $bechlemConnectRequest->method;
            $bechlemConnectRequestArray['url'] = $bechlemConnectRequest->url;
            $bechlemConnectRequestArray['data'] = $bechlemConnectRequest->data;
            $bechlemConnectRequestArray['language'] = $bechlemConnectRequest->language;
            $bechlemConnectRequestArray['description'] = $bechlemConnectRequest->description;
            $bechlemConnectRequestArray['example'] = $bechlemConnectRequest->example;
            $bechlemConnectRequestArray['options'] = $bechlemConnectRequest->options;
            $bechlemConnectRequestArray['log'] = ($bechlemConnectRequest->log == 1)? 1: 0;
            $bechlemConnectRequestArray['status'] = ($bechlemConnectRequest->status == 1)? 1: 0;
            $bechlemConnectRequestArray['created'] = empty($bechlemConnectRequest->created)? NULL: $bechlemConnectRequest->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemConnectRequestArray['modified'] = empty($bechlemConnectRequest->modified)? NULL: $bechlemConnectRequest->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $bechlemConnectRequestsArray[] = $bechlemConnectRequestArray;
        }
        $bechlemConnectRequests = ['BechlemConnectRequests' => ['BechlemConnectRequest' => $bechlemConnectRequestsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('bechlemConnectRequests'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'bechlemConnectRequests']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $bechlemConnectRequests = $this->BechlemConnectRequests->find('all');

        $bechlemConnectRequestsArray = [];
        foreach($bechlemConnectRequests as $bechlemConnectRequest) {
            $bechlemConnectRequestArray = [];
            $bechlemConnectRequestArray['id'] = $bechlemConnectRequest->id;
            $bechlemConnectRequestArray['bechlem_connect_config_id'] = $bechlemConnectRequest->bechlem_connect_config_id;
            $bechlemConnectRequestArray['name'] = $bechlemConnectRequest->name;
            $bechlemConnectRequestArray['slug'] = $bechlemConnectRequest->slug;
            $bechlemConnectRequestArray['method'] = $bechlemConnectRequest->method;
            $bechlemConnectRequestArray['url'] = $bechlemConnectRequest->url;
            $bechlemConnectRequestArray['data'] = $bechlemConnectRequest->data;
            $bechlemConnectRequestArray['language'] = $bechlemConnectRequest->language;
            $bechlemConnectRequestArray['description'] = $bechlemConnectRequest->description;
            $bechlemConnectRequestArray['example'] = $bechlemConnectRequest->example;
            $bechlemConnectRequestArray['options'] = $bechlemConnectRequest->options;
            $bechlemConnectRequestArray['log'] = ($bechlemConnectRequest->log == 1)? 1: 0;
            $bechlemConnectRequestArray['status'] = ($bechlemConnectRequest->status == 1)? 1: 0;
            $bechlemConnectRequestArray['created'] = empty($bechlemConnectRequest->created)? NULL: $bechlemConnectRequest->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemConnectRequestArray['modified'] = empty($bechlemConnectRequest->modified)? NULL: $bechlemConnectRequest->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $bechlemConnectRequestsArray[] = $bechlemConnectRequestArray;
        }
        $bechlemConnectRequests = ['BechlemConnectRequests' => ['BechlemConnectRequest' => $bechlemConnectRequestsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('bechlemConnectRequests'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'bechlemConnectRequests']);
    }
}
