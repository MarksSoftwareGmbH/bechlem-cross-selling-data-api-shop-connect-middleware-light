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
 * ProductTaxClasses Controller
 *
 * @property \BechlemConnectLight\Model\Table\ProductTaxClassesTable $ProductTaxClasses
 */
class ProductTaxClassesController extends AppController
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
            'title',
            'alias',
            'tax',
            'description',
            'created',
            'modified',
        ],
        'order' => ['title' => 'ASC']
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
        $query = $this->ProductTaxClasses
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.beforeIndexRender', $this, [
            'Query' => $query,
        ]);

        $this->set('productTaxClasses', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $productTaxClass = $this->ProductTaxClasses->get($id);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.beforeViewRender', $this, [
            'ProductTaxClass' => $productTaxClass,
        ]);

        $this->set('productTaxClass', $productTaxClass);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $productTaxClass = $this->ProductTaxClasses->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $productTaxClass = $this->ProductTaxClasses->patchEntity($productTaxClass, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.beforeAdd', $this, [
                'ProductTaxClass' => $productTaxClass,
            ]);
            if ($this->ProductTaxClasses->save($productTaxClass)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.onAddSuccess', $this, [
                    'ProductTaxClass' => $productTaxClass,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product tax class has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.onAddFailure', $this, [
                    'ProductTaxClass' => $productTaxClass,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product tax class could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.beforeAddRender', $this, [
            'ProductTaxClass' => $productTaxClass,
        ]);

        $this->set('productTaxClass', $productTaxClass);
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
        $productTaxClass = $this->ProductTaxClasses->get($id);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $productTaxClass = $this->ProductTaxClasses->patchEntity(
                $productTaxClass,
                $this->getRequest()->getdata()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.beforeEdit', $this, [
                'ProductTaxClass' => $productTaxClass,
            ]);
            if ($this->ProductTaxClasses->save($productTaxClass)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.onEditSuccess', $this, [
                    'ProductTaxClass' => $productTaxClass,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product tax class has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.onEditFailure', $this, [
                    'ProductTaxClass' => $productTaxClass,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product tax class could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.beforeEditRender', $this, [
            'ProductTaxClass' => $productTaxClass,
        ]);

        $this->set('productTaxClass', $productTaxClass);
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
        $productTaxClass = $this->ProductTaxClasses->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.beforeDelete', $this, [
            'ProductTaxClass' => $productTaxClass,
        ]);
        if ($this->ProductTaxClasses->delete($productTaxClass)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.onDeleteSuccess', $this, [
                'ProductTaxClass' => $productTaxClass,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product tax class has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTaxClasses.onDeleteFailure', $this, [
                'ProductTaxClass' => $productTaxClass,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product tax class could not be deleted. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]);
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
                $productTaxClasses = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($productTaxClasses) && !empty($productTaxClasses)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($productTaxClasses[0])) {
                    $headerArray = $this->ProductTaxClasses->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($productTaxClasses[0]));
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
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $productTaxClasses);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($productTaxClasses as $productTaxClass) {
                    $dateTime = DateTime::now();
                    $existent = $this->ProductTaxClasses
                        ->find('all')
                        ->where([
                            'title' => $productTaxClass['title'],
                            'alias' => $productTaxClass['alias'],
                        ])
                        ->first();
                    if (empty($existent)) {
                        $entity = $this->ProductTaxClasses->newEmptyEntity(); // create
                        $productTaxClass = $this->ProductTaxClasses->patchEntity(
                            $entity,
                            Hash::merge(
                                $productTaxClass,
                                [
                                    'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->ProductTaxClasses->save($productTaxClass)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->ProductTaxClasses->get($existent->id); // update
                        $productTaxClass = $this->ProductTaxClasses->patchEntity(
                            $existent,
                            Hash::merge(
                                $productTaxClass,
                                ['modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss')]
                            )
                        );
                        if ($this->ProductTaxClasses->save($productTaxClass)) {
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
        $productTaxClasses = $this->ProductTaxClasses->find('all');
        $header = $this->ProductTaxClasses->tableColumns;

        $productTaxClassesArray = [];
        foreach($productTaxClasses as $productTaxClass) {
            $productTaxClassArray = [];
            $productTaxClassArray['id'] = $productTaxClass->id;
            $productTaxClassArray['foreign_key'] = $productTaxClass->foreign_key;
            $productTaxClassArray['title'] = $productTaxClass->title;
            $productTaxClassArray['alias'] = $productTaxClass->alias;
            $productTaxClassArray['tax'] = $productTaxClass->tax;
            $productTaxClassArray['description'] = $productTaxClass->description;
            $productTaxClassArray['created'] = empty($productTaxClass->created)? NULL: $productTaxClass->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productTaxClassArray['modified'] = empty($productTaxClass->modified)? NULL: $productTaxClass->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productTaxClassesArray[] = $productTaxClassArray;
        }
        $productTaxClasses = $productTaxClassesArray;

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
        foreach ($productTaxClasses as $dataEntity) {
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
        $productTaxClasses = $this->ProductTaxClasses->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->ProductTaxClasses->tableColumns;
        $extract = [
            'id',
            'foreign_key',
            'title',
            'alias',
            'tax',
            'description',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('productTaxClasses'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'productTaxClasses',
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
        $productTaxClasses = $this->ProductTaxClasses->find('all');

        $productTaxClassesArray = [];
        foreach($productTaxClasses as $productTaxClass) {
            $productTaxClassArray = [];
            $productTaxClassArray['id'] = $productTaxClass->id;
            $productTaxClassArray['foreign_key'] = $productTaxClass->foreign_key;
            $productTaxClassArray['title'] = $productTaxClass->title;
            $productTaxClassArray['alias'] = $productTaxClass->alias;
            $productTaxClassArray['tax'] = $productTaxClass->tax;
            $productTaxClassArray['description'] = $productTaxClass->description;
            $productTaxClassArray['created'] = empty($productTaxClass->created)? NULL: $productTaxClass->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productTaxClassArray['modified'] = empty($productTaxClass->modified)? NULL: $productTaxClass->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productTaxClassesArray[] = $productTaxClassArray;
        }
        $productTaxClasses = ['ProductTaxClasses' => ['ProductTaxClass' => $productTaxClassesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('productTaxClasses'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'productTaxClasses']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $productTaxClasses = $this->ProductTaxClasses->find('all');

        $productTaxClassesArray = [];
        foreach($productTaxClasses as $productTaxClass) {
            $productTaxClassArray = [];
            $productTaxClassArray['id'] = $productTaxClass->id;
            $productTaxClassArray['foreign_key'] = $productTaxClass->foreign_key;
            $productTaxClassArray['title'] = $productTaxClass->title;
            $productTaxClassArray['alias'] = $productTaxClass->alias;
            $productTaxClassArray['tax'] = $productTaxClass->tax;
            $productTaxClassArray['description'] = $productTaxClass->description;
            $productTaxClassArray['created'] = empty($productTaxClass->created)? NULL: $productTaxClass->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productTaxClassArray['modified'] = empty($productTaxClass->modified)? NULL: $productTaxClass->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productTaxClassesArray[] = $productTaxClassArray;
        }
        $productTaxClasses = ['ProductTaxClasses' => ['ProductTaxClass' => $productTaxClassesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('productTaxClasses'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'productTaxClasses']);
    }
}
