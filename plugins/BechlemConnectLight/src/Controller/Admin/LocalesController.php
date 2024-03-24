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
 * Locales Controller
 *
 * @property \BechlemConnectLight\Model\Table\LocalesTable $Locales
 */
class LocalesController extends AppController
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
            'foreign_key',
            'name',
            'native',
            'code',
            'weight',
            'status',
        ],
        'order' => ['weight' => 'ASC']
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
     * @return void
     */
    public function index()
    {
        $query = $this->Locales
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.beforeIndexRender', $this, [
            'Query' => $query,
        ]);

        $this->set('locales', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $locale = $this->Locales->get($id, contain: [
            'Domains' => function ($q) {
                return $q->orderBy(['Domains.name' => 'ASC']);
            }
        ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.beforeViewRender', $this, ['Locale' => $locale]);

        $this->set('locale', $locale);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $locale = $this->Locales->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $associated = ['Domains'];
            $locale = $this->Locales->patchEntity($locale, $this->getRequest()->getData(), ['associated' => $associated]);
            BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.beforeAdd', $this, ['Locale' => $locale]);
            if ($this->Locales->save($locale)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.onAddSuccess', $this, ['Locale' => $locale]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The locale has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.onAddFailure', $this, ['Locale' => $locale]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The locale could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.beforeAddRender', $this, ['Locale' => $locale]);

        $this->set('locale', $locale);
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
        $locale = $this->Locales->get($id, contain: [
            'Domains' => function ($q) {
                return $q->orderBy(['Domains.name' => 'ASC']);
            }
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $associated = ['Users', 'Domains'];
            $locale = $this->Locales->patchEntity($locale, $this->getRequest()->getData(), ['associated' => $associated]);
            BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.beforeEdit', $this, ['Locale' => $locale]);
            if ($this->Locales->save($locale)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.onEditSuccess', $this, ['Locale' => $locale]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The locale has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.onEditFailure', $this, ['Locale' => $locale]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The locale could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.beforeEditRender', $this, ['Locale' => $locale]);

        $this->set('locale', $locale);
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
        $locale = $this->Locales->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.beforeDelete', $this, ['Locale' => $locale]);
        if ($this->Locales->delete($locale)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.onDeleteSuccess', $this, ['Locale' => $locale]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The locale has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Locales.onDeleteFailure', $this, ['Locale' => $locale]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The locale could not be deleted. Please, try again.'),
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
                $locales = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($locales) && !empty($locales)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($locales[0])) {
                    $headerArray = $this->Locales->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($locales[0]));
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
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $locales);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($locales as $locale) {
                    $dateTime = DateTime::now();
                    $existent = $this->Locales
                        ->find('all')
                        ->where([
                            'name' => $locale['name'],
                            'native' => $locale['native'],
                            'code' => $locale['code'],
                        ])
                        ->first();

                    if (empty($existent)) {
                        $entity = $this->Locales->newEmptyEntity(); // create
                        $locale = $this->Locales->patchEntity(
                            $entity,
                            Hash::merge(
                                $locale,
                                [
                                    'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->Locales->save($locale)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->Locales->get($existent->id); // update
                        $locale = $this->Locales->patchEntity(
                            $existent,
                            Hash::merge(
                                $locale,
                                ['modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss')]
                            )
                        );
                        if ($this->Locales->save($locale)) {
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
        $locales = $this->Locales->find('all');
        $header = $this->Locales->tableColumns;

        $localesArray = [];
        foreach($locales as $locale) {
            $localeArray = [];
            $localeArray['id'] = $locale->id;
            $localeArray['foreign_key'] = $locale->foreign_key;
            $localeArray['name'] = $locale->name;
            $localeArray['native'] = $locale->native;
            $localeArray['code'] = $locale->code;
            $localeArray['weight'] = $locale->weight;
            $localeArray['status'] = ($locale->status == 1)? 1: 0;
            $localeArray['created'] = empty($locale->created)? NULL: $locale->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $localeArray['modified'] = empty($locale->modified)? NULL: $locale->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $localesArray[] = $localeArray;
        }
        $locales = $localesArray;

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
        foreach ($locales as $dataEntity) {
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
        $locales = $this->Locales->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->Locales->tableColumns;
        $extract = [
            'id',
            'foreign_key',
            'name',
            'native',
            'code',
            'weight',
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
        $this->set(compact('locales'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'locales',
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
        $locales = $this->Locales->find('all');

        $localesArray = [];
        foreach($locales as $locale) {
            $localeArray = [];
            $localeArray['id'] = $locale->id;
            $localeArray['foreign_key'] = $locale->foreign_key;
            $localeArray['name'] = $locale->name;
            $localeArray['native'] = $locale->native;
            $localeArray['code'] = $locale->code;
            $localeArray['weight'] = $locale->weight;
            $localeArray['status'] = ($locale->status == 1)? 1: 0;
            $localeArray['created'] = empty($locale->created)? NULL: $locale->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $localeArray['modified'] = empty($locale->modified)? NULL: $locale->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $localesArray[] = $localeArray;
        }
        $locales = ['Locales' => ['Locale' => $localesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('locales'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'locales']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $locales = $this->Locales->find('all');

        $localesArray = [];
        foreach($locales as $locale) {
            $localeArray = [];
            $localeArray['id'] = $locale->id;
            $localeArray['foreign_key'] = $locale->foreign_key;
            $localeArray['name'] = $locale->name;
            $localeArray['native'] = $locale->native;
            $localeArray['code'] = $locale->code;
            $localeArray['weight'] = $locale->weight;
            $localeArray['status'] = ($locale->status == 1)? 1: 0;
            $localeArray['created'] = empty($locale->created)? NULL: $locale->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $localeArray['modified'] = empty($locale->modified)? NULL: $locale->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $localesArray[] = $localeArray;
        }
        $locales = ['Locales' => ['Locale' => $localesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('locales'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'locales']);
    }

    /**
     * Switch locale method.
     *
     * @param string $code
     *
     * @return \Cake\Http\Response|null
     */
    public function switchLocale($code = 'en_US')
    {
        if (!$this->Locale->processLocaleForSession($this, $code)) {
            $this->Flash->set(
                __d('bechlem_connect_light', 'The locale could not be changed. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]);
        }

        return $this->redirect([
            'prefix'        => 'Admin',
            'plugin'        => 'BechlemConnectLight',
            'controller'    => 'Dashboards',
            'action'        => 'dashboard',
        ]);
    }
}
