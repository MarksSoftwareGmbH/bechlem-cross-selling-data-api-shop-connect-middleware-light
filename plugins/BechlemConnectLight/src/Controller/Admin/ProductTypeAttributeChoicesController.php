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
 * ProductTypeAttributeChoices Controller
 *
 * @property \BechlemConnectLight\Model\Table\ProductTypeAttributeChoicesTable $ProductTypeAttributeChoices
 */
class ProductTypeAttributeChoicesController extends AppController
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
            'product_type_attribute_id',
            'value',
            'created',
            'modified',
            'ProductTypeAttributes.alias',
        ],
        'order' => ['product_type_attribute_id' => 'ASC']
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
        $query = $this->ProductTypeAttributeChoices
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->contain([
                'ProductTypeAttributes' => function ($q) {
                    $q->where(['ProductTypeAttributes.type' => 'select']);
                    return $q->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
                },
            ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.beforeIndexRender', $this, [
            'Query' => $query,
        ]);

        $this->set('productTypeAttributeChoices', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $productTypeAttributeChoice = $this->ProductTypeAttributeChoices->get($id, contain: [
            'ProductTypeAttributes' => function ($q) {
                $q->where(['ProductTypeAttributes.type' => 'select']);
                return $q->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
            }
        ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.beforeViewRender', $this, [
            'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
        ]);

        $this->set('productTypeAttributeChoice', $productTypeAttributeChoice);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $productTypeAttributeChoice = $this->ProductTypeAttributeChoices->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $productTypeAttributeChoice = $this->ProductTypeAttributeChoices->patchEntity(
                $productTypeAttributeChoice,
                $this->getRequest()->getData()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.beforeAdd', $this, [
                'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
            ]);
            if ($this->ProductTypeAttributeChoices->save($productTypeAttributeChoice)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.onAddSuccess', $this, [
                    'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute choice has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.onAddFailure', $this, [
                    'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute choice could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $productTypeAttributes = $this->ProductTypeAttributeChoices->ProductTypeAttributes
            ->find('list', order: ['ProductTypeAttributes.alias' => 'ASC'], keyField: 'id', valueField: 'title_alias')
            ->where(['ProductTypeAttributes.type' => 'select']);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.beforeAddRender', $this, [
            'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
            'ProductTypeAttributes' => $productTypeAttributes,
        ]);

        $this->set(compact('productTypeAttributeChoice', 'productTypeAttributes'));
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
        $productTypeAttributeChoice = $this->ProductTypeAttributeChoices->get($id, contain: [
            'ProductTypeAttributes' => function ($q) {
                $q->where(['ProductTypeAttributes.type' => 'select']);
                return $q->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
            }
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $productTypeAttributeChoice = $this->ProductTypeAttributeChoices->patchEntity(
                $productTypeAttributeChoice,
                $this->getRequest()->getData()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.beforeEdit', $this, [
                'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
            ]);
            if ($this->ProductTypeAttributeChoices->save($productTypeAttributeChoice)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.onEditSuccess', $this, [
                    'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute choice has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.onEditFailure', $this, [
                    'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute choice could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $productTypeAttributes = $this->ProductTypeAttributeChoices->ProductTypeAttributes
            ->find('list', order: ['ProductTypeAttributes.alias' => 'ASC'], keyField: 'id', valueField: 'title_alias')
            ->where(['ProductTypeAttributes.type' => 'select']);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.beforeEditRender', $this, [
            'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
            'ProductTypeAttributes' => $productTypeAttributes,
        ]);

        $this->set(compact('productTypeAttributeChoice', 'productTypeAttributes'));
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
        $productTypeAttributeChoice = $this->ProductTypeAttributeChoices->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.beforeDelete', $this, [
            'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
        ]);
        if ($this->ProductTypeAttributeChoices->delete($productTypeAttributeChoice)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.onDeleteSuccess', $this, [
                'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product type attribute choice has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributeChoices.onDeleteFailure', $this, [
                'ProductTypeAttributeChoice' => $productTypeAttributeChoice,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product type attribute choice could not be deleted. Please, try again.'),
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
                $productTypeAttributeChoices = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($productTypeAttributeChoices) && !empty($productTypeAttributeChoices)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($productTypeAttributeChoices[0])) {
                    $headerArray = $this->ProductTypeAttributeChoices->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($productTypeAttributeChoices[0]));
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
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $productTypeAttributeChoices);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($productTypeAttributeChoices as $productTypeAttributeChoice) {
                    $dateTime = DateTime::now();
                    $existent = $this->ProductTypeAttributeChoices
                        ->find('all')
                        ->where([
                            'product_type_attribute_id' => $productTypeAttributeChoice['product_type_attribute_id'],
                            'value' => $productTypeAttributeChoice['value'],
                        ])
                        ->first();

                    if (empty($existent)) {
                        $entity = $this->ProductTypeAttributeChoices->newEmptyEntity(); // create
                        $productTypeAttributeChoice = $this->ProductTypeAttributeChoices->patchEntity(
                            $entity,
                            Hash::merge(
                                $productTypeAttributeChoice,
                                [
                                    'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->ProductTypeAttributeChoices->save($productTypeAttributeChoice)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->ProductTypeAttributeChoices->get($existent->id); // update
                        $productTypeAttributeChoice = $this->ProductTypeAttributeChoices->patchEntity(
                            $existent,
                            Hash::merge(
                                $productTypeAttributeChoice,
                                ['modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss')]
                            )
                        );
                        if ($this->ProductTypeAttributeChoices->save($productTypeAttributeChoice)) {
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
        $productTypeAttributeChoices = $this->ProductTypeAttributeChoices->find('all');
        $header = $this->ProductTypeAttributeChoices->tableColumns;

        $productTypeAttributeChoicesArray = [];
        foreach($productTypeAttributeChoices as $productTypeAttributeChoice) {
            $productTypeAttributeChoiceArray = [];
            $productTypeAttributeChoiceArray['id'] = $productTypeAttributeChoice->id;
            $productTypeAttributeChoiceArray['product_type_attribute_id'] = $productTypeAttributeChoice->product_type_attribute_id;
            $productTypeAttributeChoiceArray['value'] = $productTypeAttributeChoice->value;
            $productTypeAttributeChoiceArray['created'] = empty($productTypeAttributeChoice->created)? NULL: $productTypeAttributeChoice->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productTypeAttributeChoiceArray['modified'] = empty($productTypeAttributeChoice->modified)? NULL: $productTypeAttributeChoice->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productTypeAttributeChoicesArray[] = $productTypeAttributeChoiceArray;
        }
        $productTypeAttributeChoices = $productTypeAttributeChoicesArray;

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
        foreach ($productTypeAttributeChoices as $dataEntity) {
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
        $productTypeAttributeChoices = $this->ProductTypeAttributeChoices->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->ProductTypeAttributeChoices->tableColumns;
        $extract = [
            'id',
            'product_type_attribute_id',
            'value',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('productTypeAttributeChoices'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'productTypeAttributeChoices',
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
        $productTypeAttributeChoices = $this->ProductTypeAttributeChoices->find('all');

        $productTypeAttributeChoicesArray = [];
        foreach($productTypeAttributeChoices as $productTypeAttributeChoice) {
            $productTypeAttributeChoiceArray = [];
            $productTypeAttributeChoiceArray['id'] = $productTypeAttributeChoice->id;
            $productTypeAttributeChoiceArray['product_type_attribute_id'] = $productTypeAttributeChoice->product_type_attribute_id;
            $productTypeAttributeChoiceArray['value'] = $productTypeAttributeChoice->value;
            $productTypeAttributeChoiceArray['created'] = empty($productTypeAttributeChoice->created)? NULL: $productTypeAttributeChoice->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productTypeAttributeChoiceArray['modified'] = empty($productTypeAttributeChoice->modified)? NULL: $productTypeAttributeChoice->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productTypeAttributeChoicesArray[] = $productTypeAttributeChoiceArray;
        }
        $productTypeAttributeChoices = ['ProductTypeAttributeChoices' => ['ProductTypeAttributeChoice' => $productTypeAttributeChoicesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('productTypeAttributeChoices'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'productTypeAttributeChoices']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $productTypeAttributeChoices = $this->ProductTypeAttributeChoices->find('all');

        $productTypeAttributeChoicesArray = [];
        foreach($productTypeAttributeChoices as $productTypeAttributeChoice) {
            $productTypeAttributeChoiceArray = [];
            $productTypeAttributeChoiceArray['id'] = $productTypeAttributeChoice->id;
            $productTypeAttributeChoiceArray['product_type_attribute_id'] = $productTypeAttributeChoice->product_type_attribute_id;
            $productTypeAttributeChoiceArray['value'] = $productTypeAttributeChoice->value;
            $productTypeAttributeChoiceArray['created'] = empty($productTypeAttributeChoice->created)? NULL: $productTypeAttributeChoice->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productTypeAttributeChoiceArray['modified'] = empty($productTypeAttributeChoice->modified)? NULL: $productTypeAttributeChoice->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productTypeAttributeChoicesArray[] = $productTypeAttributeChoiceArray;
        }
        $productTypeAttributeChoices = ['ProductTypeAttributeChoices' => ['ProductTypeAttributeChoice' => $productTypeAttributeChoicesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('productTypeAttributeChoices'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'productTypeAttributeChoices']);
    }
}
