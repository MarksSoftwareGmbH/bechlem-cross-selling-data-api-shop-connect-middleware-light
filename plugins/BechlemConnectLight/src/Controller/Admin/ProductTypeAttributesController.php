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
 * ProductTypeAttributes Controller
 *
 * @property \BechlemConnectLight\Model\Table\ProductTypeAttributesTable $ProductTypeAttributes
 */
class ProductTypeAttributesController extends AppController
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
            'type',
            'description',
            'empty_value',
            'wysiwyg',
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
        $query = $this->ProductTypeAttributes
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->contain([
                'ProductTypeAttributeChoices' => function ($q) {
                    return $q->orderBy(['ProductTypeAttributeChoices.value' => 'ASC']);
                }
            ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.beforeIndexRender', $this, [
            'Query' => $query,
        ]);

        $this->set('productTypeAttributes', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $productTypeAttribute = $this->ProductTypeAttributes->get($id, contain: [
            'ProductTypeAttributeChoices' => function ($q) {
                return $q->orderBy(['ProductTypeAttributeChoices.value' => 'ASC']);
            },
            'ProductTypes' => function ($q) {
                return $q->orderBy(['ProductTypes.title' => 'ASC']);
            },
            'ProductProductTypeAttributeValues'
        ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.beforeViewRender', $this, [
            'ProductTypeAttribute' => $productTypeAttribute,
        ]);

        $this->set('productTypeAttribute', $productTypeAttribute);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $productTypeAttribute = $this->ProductTypeAttributes->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $productTypeAttribute = $this->ProductTypeAttributes->patchEntity(
                $productTypeAttribute,
                $this->getRequest()->getData()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.beforeAdd', $this, [
                'ProductTypeAttribute' => $productTypeAttribute,
            ]);

            if ($this->ProductTypeAttributes->save($productTypeAttribute)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.onAddSuccess', $this, [
                    'ProductTypeAttribute' => $productTypeAttribute,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.onAddFailure', $this, [
                    'ProductTypeAttribute' => $productTypeAttribute,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }


        $productTypes = $this->ProductTypeAttributes->ProductTypes
            ->find('list', order: ['ProductTypes.alias' => 'ASC'], keyField: 'id', valueField: 'title_alias')
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.beforeAddRender', $this, [
            'ProductTypeAttribute' => $productTypeAttribute,
            'ProductTypes' => $productTypes,
        ]);

        $this->set(compact('productTypeAttribute', 'productTypes'));
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
        $productTypeAttribute = $this->ProductTypeAttributes->get($id, contain: ['ProductTypes']);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $productTypeAttribute = $this->ProductTypeAttributes->patchEntity(
                $productTypeAttribute,
                $this->getRequest()->getData()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.beforeEdit', $this, [
                'ProductTypeAttribute' => $productTypeAttribute,
            ]);
            if ($this->ProductTypeAttributes->save($productTypeAttribute)) {

                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.onEditSuccess', $this, [
                    'ProductTypeAttribute' => $productTypeAttribute,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.onEditFailure', $this, [
                    'ProductTypeAttribute' => $productTypeAttribute,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $productTypes = $this->ProductTypeAttributes->ProductTypes
            ->find('list', order: ['ProductTypes.alias' => 'ASC'], keyField: 'id', valueField: 'title_alias');

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.beforeEditRender', $this, [
            'ProductTypeAttribute' => $productTypeAttribute,
            'ProductTypes' => $productTypes,
        ]);

        $this->set(compact('productTypeAttribute', 'productTypes'));
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
        $productTypeAttribute = $this->ProductTypeAttributes->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.beforeDelete', $this, [
            'ProductTypeAttribute' => $productTypeAttribute,
        ]);
        if ($this->ProductTypeAttributes->delete($productTypeAttribute)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.onDeleteSuccess', $this, [
                'ProductTypeAttribute' => $productTypeAttribute,
            ]);
            $this->Flash->set(__d('bechlem_connect_light', 'The product type attribute has been deleted.'), [
                'element' => 'default',
                'params' => ['class' => 'success']
            ]);
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductTypeAttributes.onDeleteFailure', $this, [
                'ProductTypeAttribute' => $productTypeAttribute,
            ]);
            $this->Flash->set(__d('bechlem_connect_light', 'The product type attribute could not be deleted. Please, try again.'), [
                'element' => 'default',
                'params' => ['class' => 'error']
            ]);
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
                $productTypeAttributes = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($productTypeAttributes) && !empty($productTypeAttributes)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($productTypeAttributes[0])) {
                    $headerArray = $this->ProductTypeAttributes->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($productTypeAttributes[0]));
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
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $productTypeAttributes);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($productTypeAttributes as $productTypeAttribute) {
                    $dateTime = DateTime::now();
                    $existent = $this->ProductTypeAttributes
                        ->find('all')
                        ->where([
                            'title' => $productTypeAttribute['title'],
                            'alias' => $productTypeAttribute['alias'],
                        ])
                        ->first();

                    if (empty($existent)) {
                        $entity = $this->ProductTypeAttributes->newEmptyEntity(); // create
                        $productTypeAttribute = $this->ProductTypeAttributes->patchEntity(
                            $entity,
                            Hash::merge(
                                $productTypeAttribute,
                                [
                                    'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->ProductTypeAttributes->save($productTypeAttribute)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->ProductTypeAttributes->get($existent->id); // update
                        $productTypeAttribute = $this->ProductTypeAttributes->patchEntity(
                            $existent,
                            Hash::merge(
                                $productTypeAttribute,
                                ['modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss')]
                            )
                        );
                        if ($this->ProductTypeAttributes->save($productTypeAttribute)) {
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
        $productTypeAttributes = $this->ProductTypeAttributes->find('all');
        $header = $this->ProductTypeAttributes->tableColumns;

        $productTypeAttributesArray = [];
        foreach($productTypeAttributes as $productTypeAttribute) {
            $productTypeAttributeArray = [];
            $productTypeAttributeArray['id'] = $productTypeAttribute->id;
            $productTypeAttributeArray['foreign_key'] = $productTypeAttribute->foreign_key;
            $productTypeAttributeArray['title'] = $productTypeAttribute->title;
            $productTypeAttributeArray['alias'] = $productTypeAttribute->alias;
            $productTypeAttributeArray['type'] = $productTypeAttribute->type;
            $productTypeAttributeArray['description'] = $productTypeAttribute->description;
            $productTypeAttributeArray['empty_value'] = ($productTypeAttribute->empty_value == 1)? 1: 0;
            $productTypeAttributeArray['wysiwyg'] = ($productTypeAttribute->wysiwyg == 1)? 1: 0;
            $productTypeAttributeArray['created'] = empty($productTypeAttribute->created)? NULL: $productTypeAttribute->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productTypeAttributeArray['modified'] = empty($productTypeAttribute->modified)? NULL: $productTypeAttribute->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productTypeAttributesArray[] = $productTypeAttributeArray;
        }
        $productTypeAttributes = $productTypeAttributesArray;

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
        foreach ($productTypeAttributes as $dataEntity) {
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
        $productTypeAttributes = $this->ProductTypeAttributes->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->ProductTypeAttributes->tableColumns;
        $extract = [
            'id',
            'foreign_key',
            'title',
            'alias',
            'type',
            'description',
            function ($row) {
                return ($row['empty_value'] == 1)? 1: 0;
            },
            function ($row) {
                return ($row['wysiwyg'] == 1)? 1: 0;
            },
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('productTypeAttributes'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'productTypeAttributes',
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
        $productTypeAttributes = $this->ProductTypeAttributes->find('all');

        $productTypeAttributesArray = [];
        foreach($productTypeAttributes as $productTypeAttribute) {
            $productTypeAttributeArray = [];
            $productTypeAttributeArray['id'] = $productTypeAttribute->id;
            $productTypeAttributeArray['foreign_key'] = $productTypeAttribute->foreign_key;
            $productTypeAttributeArray['title'] = $productTypeAttribute->title;
            $productTypeAttributeArray['alias'] = $productTypeAttribute->alias;
            $productTypeAttributeArray['type'] = $productTypeAttribute->type;
            $productTypeAttributeArray['description'] = $productTypeAttribute->description;
            $productTypeAttributeArray['empty_value'] = ($productTypeAttribute->empty_value == 1)? 1: 0;
            $productTypeAttributeArray['wysiwyg'] = ($productTypeAttribute->wysiwyg == 1)? 1: 0;
            $productTypeAttributeArray['created'] = empty($productTypeAttribute->created)? NULL: $productTypeAttribute->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productTypeAttributeArray['modified'] = empty($productTypeAttribute->modified)? NULL: $productTypeAttribute->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productTypeAttributesArray[] = $productTypeAttributeArray;
        }
        $productTypeAttributes = ['ProductTypeAttributes' => ['ProductTypeAttribute' => $productTypeAttributesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('productTypeAttributes'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'productTypeAttributes']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $productTypeAttributes = $this->ProductTypeAttributes->find('all');

        $productTypeAttributesArray = [];
        foreach($productTypeAttributes as $productTypeAttribute) {
            $productTypeAttributeArray = [];
            $productTypeAttributeArray['id'] = $productTypeAttribute->id;
            $productTypeAttributeArray['foreign_key'] = $productTypeAttribute->foreign_key;
            $productTypeAttributeArray['title'] = $productTypeAttribute->title;
            $productTypeAttributeArray['alias'] = $productTypeAttribute->alias;
            $productTypeAttributeArray['type'] = $productTypeAttribute->type;
            $productTypeAttributeArray['description'] = $productTypeAttribute->description;
            $productTypeAttributeArray['empty_value'] = ($productTypeAttribute->empty_value == 1)? 1: 0;
            $productTypeAttributeArray['wysiwyg'] = ($productTypeAttribute->wysiwyg == 1)? 1: 0;
            $productTypeAttributeArray['created'] = empty($productTypeAttribute->created)? NULL: $productTypeAttribute->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productTypeAttributeArray['modified'] = empty($productTypeAttribute->modified)? NULL: $productTypeAttribute->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productTypeAttributesArray[] = $productTypeAttributeArray;
        }
        $productTypeAttributes = ['ProductTypeAttributes' => ['ProductTypeAttribute' => $productTypeAttributesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('productTypeAttributes'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'productTypeAttributes']);
    }
}
