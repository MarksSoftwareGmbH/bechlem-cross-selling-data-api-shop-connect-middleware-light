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
 * ProductBrands Controller
 *
 * @property \BechlemConnectLight\Model\Table\ProductBrandsTable $ProductBrands
 */
class ProductBrandsController extends AppController
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
            'slug',
            'website',
            'image',
            'logo',
            'status',
            'created',
            'modified',
        ],
        'order' => ['name' => 'ASC']
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
        $query = $this->ProductBrands
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.beforeIndexRender', $this, [
            'Query' => $query,
        ]);

        $this->set('productBrands', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $productBrand = $this->ProductBrands->get($id, contain: ['ProductBrandsProducts']);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.beforeViewRender', $this, [
            'ProductBrand' => $productBrand,
        ]);

        $this->set('productBrand', $productBrand);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $productBrand = $this->ProductBrands->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $productBrand = $this->ProductBrands->patchEntity($productBrand, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.beforeAdd', $this, [
                'ProductBrand' => $productBrand,
            ]);
            if ($this->ProductBrands->save($productBrand)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.onAddSuccess', $this, [
                    'ProductBrand' => $productBrand,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product brand has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.onAddFailure', $this, [
                    'ProductBrand' => $productBrand,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product brand could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.beforeAddRender', $this, [
            'ProductBrand' => $productBrand,
        ]);

        $this->set('productBrand', $productBrand);
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
        $productBrand = $this->ProductBrands->get($id);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $productBrand = $this->ProductBrands->patchEntity(
                $productBrand,
                $this->getRequest()->getdata()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.beforeEdit', $this, [
                'ProductBrand' => $productBrand,
            ]);
            if ($this->ProductBrands->save($productBrand)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.onEditSuccess', $this, [
                    'ProductBrand' => $productBrand,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product brand has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.onEditFailure', $this, [
                    'ProductBrand' => $productBrand,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product brand could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.beforeEditRender', $this, [
            'ProductBrand' => $productBrand,
        ]);

        $this->set('productBrand', $productBrand);
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
        $productBrand = $this->ProductBrands->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.beforeDelete', $this, [
            'ProductBrand' => $productBrand,
        ]);
        if ($this->ProductBrands->delete($productBrand)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.onDeleteSuccess', $this, [
                'ProductBrand' => $productBrand,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product brand has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductBrands.onDeleteFailure', $this, [
                'ProductBrand' => $productBrand,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product brand could not be deleted. Please, try again.'),
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
                $productBrands = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($productBrands) && !empty($productBrands)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($productBrands[0])) {
                    $headerArray = $this->ProductBrands->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($productBrands[0]));
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
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $productBrands);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($productBrands as $productBrand) {
                    $dateTime = DateTime::now();
                    $existent = $this->ProductBrands
                        ->find('all')
                        ->where([
                            'name' => $productBrand['name'],
                            'slug' => $productBrand['slug'],
                        ])
                        ->first();
                    if (empty($existent)) {
                        $entity = $this->ProductBrands->newEmptyEntity(); // create
                        $productBrand = $this->ProductBrands->patchEntity(
                            $entity,
                            Hash::merge(
                                $productBrand,
                                [
                                    'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->ProductBrands->save($productBrand)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->ProductBrands->get($existent->id); // update
                        $productBrand = $this->ProductBrands->patchEntity(
                            $existent,
                            Hash::merge(
                                $productBrand,
                                ['modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss')]
                            )
                        );
                        if ($this->ProductBrands->save($productBrand)) {
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
        $productBrands = $this->ProductBrands->find('all');
        $header = $this->ProductBrands->tableColumns;

        $productBrandsArray = [];
        foreach($productBrands as $productBrand) {
            $productBrandArray = [];
            $productBrandArray['id'] = $productBrand->id;
            $productBrandArray['foreign_key'] = $productBrand->foreign_key;
            $productBrandArray['name'] = $productBrand->name;
            $productBrandArray['slug'] = $productBrand->slug;
            $productBrandArray['website'] = $productBrand->website;
            $productBrandArray['image'] = $productBrand->image;
            $productBrandArray['logo'] = $productBrand->logo;
            $productBrandArray['status'] = ($productBrand->status == 1)? 1: 0;
            $productBrandArray['created'] = empty($productBrand->created)? NULL: $productBrand->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productBrandArray['modified'] = empty($productBrand->modified)? NULL: $productBrand->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productBrandsArray[] = $productBrandArray;
        }
        $productBrands = $productBrandsArray;

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
        foreach ($productBrands as $dataEntity) {
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
        $productBrands = $this->ProductBrands->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->ProductBrands->tableColumns;
        $extract = [
            'id',
            'foreign_key',
            'name',
            'slug',
            'website',
            'image',
            'logo',
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
        $this->set(compact('productBrands'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'productBrands',
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
        $productBrands = $this->ProductBrands->find('all');

        $productBrandsArray = [];
        foreach($productBrands as $productBrand) {
            $productBrandArray = [];
            $productBrandArray['id'] = $productBrand->id;
            $productBrandArray['foreign_key'] = $productBrand->foreign_key;
            $productBrandArray['name'] = $productBrand->name;
            $productBrandArray['slug'] = $productBrand->slug;
            $productBrandArray['website'] = $productBrand->website;
            $productBrandArray['image'] = $productBrand->image;
            $productBrandArray['logo'] = $productBrand->logo;
            $productBrandArray['status'] = ($productBrand->status == 1)? 1: 0;
            $productBrandArray['created'] = empty($productBrand->created)? NULL: $productBrand->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productBrandArray['modified'] = empty($productBrand->modified)? NULL: $productBrand->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productBrandsArray[] = $productBrandArray;
        }
        $productBrands = ['ProductBrands' => ['ProductBrand' => $productBrandsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('productBrands'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'productBrands']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $productBrands = $this->ProductBrands->find('all');

        $productBrandsArray = [];
        foreach($productBrands as $productBrand) {
            $productBrandArray = [];
            $productBrandArray['id'] = $productBrand->id;
            $productBrandArray['foreign_key'] = $productBrand->foreign_key;
            $productBrandArray['name'] = $productBrand->name;
            $productBrandArray['slug'] = $productBrand->slug;
            $productBrandArray['website'] = $productBrand->website;
            $productBrandArray['image'] = $productBrand->image;
            $productBrandArray['logo'] = $productBrand->logo;
            $productBrandArray['status'] = ($productBrand->status == 1)? 1: 0;
            $productBrandArray['created'] = empty($productBrand->created)? NULL: $productBrand->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productBrandArray['modified'] = empty($productBrand->modified)? NULL: $productBrand->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productBrandsArray[] = $productBrandArray;
        }
        $productBrands = ['ProductBrands' => ['ProductBrand' => $productBrandsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('productBrands'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'productBrands']);
    }
}
