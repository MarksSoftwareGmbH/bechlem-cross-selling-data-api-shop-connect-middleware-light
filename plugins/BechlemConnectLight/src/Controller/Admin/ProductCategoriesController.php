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
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * ProductCategories Controller
 *
 * @property \BechlemConnectLight\Model\Table\ProductCategoriesTable $ProductCategories
 */
class ProductCategoriesController extends AppController
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
            'parent_id',
            'foreign_key',
            'lft',
            'rght',
            'name',
            'slug',
            'background_image',
            'locale',
            'status',
            'created',
            'modified',
        ],
        'order' => ['lft' => 'ASC']
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
        $query = $this->ProductCategories
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->contain(['ParentProductCategories']);

        $Locales = TableRegistry::getTableLocator()->get('BechlemConnectLight.Locales');
        $locales = $Locales
            ->find('list',
                conditions: ['Locales.status' => 1],
                order: ['Locales.weight' => 'ASC'],
                keyField: 'code',
                valueField: 'name'
            )
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.beforeIndexRender', $this, [
            'Query'     => $query,
            'Locales'   => $locales,
        ]);

        $this->set('productCategories', $this->paginate($query));
        $this->set('locales', $locales);
    }

    /**
     * Ajax move method
     *
     * @return void
     */
    public function ajaxMove()
    {
        if (!$this->getRequest()->is('ajax')) {
            $this->Flash->set(
                __d('bechlem_connect_light', 'Invalid request. Please, try again with a ajax request.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        $response = false;
        if ($this->getRequest()->getQuery('draggedLft') > $this->getRequest()->getQuery('siblingLft')) {
            $movement = $this->ProductCategories
                ->find()
                ->where([
                    'ProductCategories.lft >' => $this->getRequest()->getQuery('siblingLft'),
                    'ProductCategories.lft <=' => $this->getRequest()->getQuery('draggedLft')
                ])
                ->count();
            $productCategory = $this->ProductCategories->get($this->getRequest()->getQuery('draggedId'));

            if ($this->ProductCaregories->moveUp($productCategory, $movement)) {
                $response = true;
            }
        }

        if ($this->getRequest()->getQuery('draggedLft') < $this->getRequest()->getQuery('siblingLft')) {
            $movement = $this->ProductCategories
                ->find()
                ->where([
                    'ProductCategories.lft <' => $this->getRequest()->getQuery('siblingLft'),
                    'ProductCategories.lft >=' => $this->getRequest()->getQuery('draggedLft')
                ])
                ->count();
            $productCategory = $this->ProductCategories->get($this->getRequest()->getQuery('draggedId'));

            if ($this->ProductCategories->moveDown($productCategory, $movement)) {
                $response = true;
            }
        }

        $this->set('response', $response);
        $this->set('_serialize', ['response']);
        $this->viewBuilder()->setPlugin('BechlemConnectLight')->setLayout(null);
    }

    /**
     * Move up method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function moveUp(int $id = null)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        $productCategory = $this->ProductCategories->get($id);
        if ($this->ProductCategories->moveUp($productCategory)) {
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product category has been moved up.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product category could not be moved up. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect($this->referer(['action' => 'index']));
    }

    /**
     * Move down method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function moveDown(int $id = null)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        $productCategory = $this->ProductCategories->get($id);
        if ($this->ProductCategories->moveDown($productCategory)) {
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product category has been moved down.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product category could not be moved down. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect($this->referer(['action' => 'index']));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $productCategory = $this->ProductCategories->get($id, contain: [
            'ParentProductCategories',
            'ChildProductCategories',
        ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.beforeViewRender', $this, [
            'ProductCategory' => $productCategory,
        ]);

        $this->set('productCategory', $productCategory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $productCategory = $this->ProductCategories->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $productCategory = $this->ProductCategories->patchEntity($productCategory, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.beforeAdd', $this, [
                'ProductCategory' => $productCategory,
            ]);
            if ($this->ProductCategories->save($productCategory)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.onAddSuccess', $this, [
                    'ProductCategory' => $productCategory,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product category has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.onAddFailure', $this, [
                    'ProductCategory' => $productCategory,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product category could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $parentProductCategories = $this->ProductCategories->ParentProductCategories
            ->find('treeList',
                keyPath: 'id',
                valuePath: 'name_locale',
                spacer: '-> '
            )
            ->where(['ParentProductCategories.status' => 1]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.beforeAddRender', $this, [
            'ProductCategory' => $productCategory,
            'ParentProductCategories' => $parentProductCategories,
        ]);

        $this->set(compact('productCategory', 'parentProductCategories'));
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
        $productCategory = $this->ProductCategories->get($id, contain: [
            'ParentProductCategories',
            'ChildProductCategories',
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $productCategory = $this->ProductCategories->patchEntity(
                $productCategory,
                $this->getRequest()->getdata()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.beforeEdit', $this, [
                'ProductCategory' => $productCategory,
            ]);
            if ($this->ProductCategories->save($productCategory)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.onEditSuccess', $this, [
                    'ProductCategory' => $productCategory,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product category has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.onEditFailure', $this, [
                    'ProductCategory' => $productCategory,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product category could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $parentProductCategories = $this->ProductCategories->ParentProductCategories
            ->find('treeList',
                keyPath: 'id',
                valuePath: 'name_locale',
                spacer: '-> '
            )
            ->where([
                'id !=' => $productCategory->id,
                'ParentProductCategories.status' => 1,
            ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.beforeEditRender', $this, [
            'ProductCategory' => $productCategory,
            'ParentProductCategories' => $parentProductCategories,
        ]);

        $this->set(compact('productCategory', 'parentProductCategories'));
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
        $productCategory = $this->ProductCategories->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.beforeDelete', $this, [
            'ProductCategory' => $productCategory,
        ]);
        if ($this->ProductCategories->delete($productCategory)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.onDeleteSuccess', $this, [
                'ProductCategory' => $productCategory,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product category has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductCategories.onDeleteFailure', $this, [
                'ProductCategory' => $productCategory,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product category could not be deleted. Please, try again.'),
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
                $productCategories = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($productCategories) && !empty($productCategories)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($productCategories[0])) {
                    $headerArray = $this->ProductCategories->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($productCategories[0]));
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
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $productCategories);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($productCategories as $productCategory) {
                    $dateTime = DateTime::now();
                    $existent = $this->ProductCategories
                        ->find('all')
                        ->where([
                            'foreign_key'   => $productCategory['foreign_key'],
                            'name'          => $productCategory['name'],
                            'slug'          => $productCategory['slug'],
                            'locale'        => $productCategory['locale'],
                        ])
                        ->first();
                    if (empty($existent)) {
                        $entity = $this->ProductCategories->newEmptyEntity(); // create
                        $productCategory = $this->ProductCategories->patchEntity(
                            $entity,
                            Hash::merge(
                                $productCategory,
                                [
                                    'slug'      => Text::slug(strtolower($productCategory['slug'])),
                                    'created'   => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified'  => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->ProductCategories->save($productCategory)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->ProductCategories->get($existent->id); // update
                        $productCategory = $this->ProductCategories->patchEntity(
                            $existent,
                            Hash::merge(
                                $productCategory,
                                [
                                    'slug'      => Text::slug(strtolower($productCategory['slug'])),
                                    'modified'  => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->ProductCategories->save($productCategory)) {
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
        $productCategories = $this->ProductCategories->find('all');
        $header = $this->ProductCategories->tableColumns;

        $productCategoriesArray = [];
        foreach($productCategories as $productCategory) {
            $productCategoryArray = [];
            $productCategoryArray['id'] = $productCategory->id;
            $productCategoryArray['parent_id'] = $productCategory->parent_id;
            $productCategoryArray['foreign_key'] = $productCategory->foreign_key;
            $productCategoryArray['lft'] = $productCategory->lft;
            $productCategoryArray['rght'] = $productCategory->rght;
            $productCategoryArray['name'] = $productCategory->name;
            $productCategoryArray['slug'] = $productCategory->slug;
            $productCategoryArray['description'] = $productCategory->description;
            $productCategoryArray['background_image'] = $productCategory->background_image;
            $productCategoryArray['meta_description'] = $productCategory->meta_description;
            $productCategoryArray['meta_keywords'] = $productCategory->meta_keywords;
            $productCategoryArray['locale'] = $productCategory->locale;
            $productCategoryArray['status'] = ($productCategory->status == 1)? 1: 0;
            $productCategoryArray['created'] = empty($productCategory->created)? NULL: $productCategory->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productCategoryArray['modified'] = empty($productCategory->modified)? NULL: $productCategory->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productCategoriesArray[] = $productCategoryArray;
        }
        $productCategories = $productCategoriesArray;

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
        foreach ($productCategories as $dataEntity) {
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
        $productCategories = $this->ProductCategories->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->ProductCategories->tableColumns;
        $extract = [
            'id',
            'parent_id',
            'foreign_key',
            'lft',
            'rght',
            'name',
            'slug',
            'description',
            'background_image',
            'meta_description',
            'meta_keywords',
            'locale',
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
        $this->set(compact('productCategories'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'productCategories',
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
        $productCategories = $this->ProductCategories->find('all');

        $productCategoriesArray = [];
        foreach($productCategories as $productCategory) {
            $productCategoryArray = [];
            $productCategoryArray['id'] = $productCategory->id;
            $productCategoryArray['parent_id'] = $productCategory->parent_id;
            $productCategoryArray['foreign_key'] = $productCategory->foreign_key;
            $productCategoryArray['lft'] = $productCategory->lft;
            $productCategoryArray['rght'] = $productCategory->rght;
            $productCategoryArray['name'] = $productCategory->name;
            $productCategoryArray['slug'] = $productCategory->slug;
            $productCategoryArray['description'] = $productCategory->description;
            $productCategoryArray['background_image'] = $productCategory->background_image;
            $productCategoryArray['meta_description'] = $productCategory->meta_description;
            $productCategoryArray['meta_keywords'] = $productCategory->meta_keywords;
            $productCategoryArray['locale'] = $productCategory->locale;
            $productCategoryArray['status'] = ($productCategory->status == 1)? 1: 0;
            $productCategoryArray['created'] = empty($productCategory->created)? NULL: $productCategory->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productCategoryArray['modified'] = empty($productCategory->modified)? NULL: $productCategory->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productCategoriesArray[] = $productCategoryArray;
        }
        $productCategories = ['ProductCategories' => ['ProductCategory' => $productCategoriesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('productCategories'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'productCategories']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $productCategories = $this->ProductCategories->find('all');

        $productCategoriesArray = [];
        foreach($productCategories as $productCategory) {
            $productCategoryArray = [];
            $productCategoryArray['id'] = $productCategory->id;
            $productCategoryArray['parent_id'] = $productCategory->parent_id;
            $productCategoryArray['foreign_key'] = $productCategory->foreign_key;
            $productCategoryArray['lft'] = $productCategory->lft;
            $productCategoryArray['rght'] = $productCategory->rght;
            $productCategoryArray['name'] = $productCategory->name;
            $productCategoryArray['slug'] = $productCategory->slug;
            $productCategoryArray['description'] = $productCategory->description;
            $productCategoryArray['background_image'] = $productCategory->background_image;
            $productCategoryArray['meta_description'] = $productCategory->meta_description;
            $productCategoryArray['meta_keywords'] = $productCategory->meta_keywords;
            $productCategoryArray['locale'] = $productCategory->locale;
            $productCategoryArray['status'] = ($productCategory->status == 1)? 1: 0;
            $productCategoryArray['created'] = empty($productCategory->created)? NULL: $productCategory->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productCategoryArray['modified'] = empty($productCategory->modified)? NULL: $productCategory->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productCategoriesArray[] = $productCategoryArray;
        }
        $productCategories = ['ProductCategories' => ['ProductCategory' => $productCategoriesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('productCategories'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'productCategories']);
    }
}
