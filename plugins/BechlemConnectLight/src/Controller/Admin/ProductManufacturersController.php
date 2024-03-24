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
use Cake\Utility\Text;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * ProductManufacturers Controller
 *
 * @property \BechlemConnectLight\Model\Table\ProductManufacturersTable $ProductManufacturers
 */
class ProductManufacturersController extends AppController
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
            'logo',
            'image',
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
        $query = $this->ProductManufacturers
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.beforeIndexRender', $this, [
            'Query' => $query,
        ]);

        $this->set('productManufacturers', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $productManufacturer = $this->ProductManufacturers->get($id);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.beforeViewRender', $this, [
            'ProductManufacturer' => $productManufacturer
        ]);

        $this->set('productManufacturer', $productManufacturer);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function add()
    {
        $productManufacturer = $this->ProductManufacturers->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $productManufacturer = $this->ProductManufacturers->patchEntity($productManufacturer, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.beforeAdd', $this, [
                'ProductManufacturer' => $productManufacturer,
            ]);
            if ($this->ProductManufacturers->save($productManufacturer)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.onAddSuccess', $this, [
                    'ProductManufacturer' => $productManufacturer,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product manufacturer has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.onAddFailure', $this, [
                    'ProductManufacturer' => $productManufacturer,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product manufacturer could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.beforeAddRender', $this, [
            'ProductManufacturer' => $productManufacturer,
        ]);

        $this->set('productManufacturer', $productManufacturer);
    }

    /**
     * Edit method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        $productManufacturer = $this->ProductManufacturers->get($id);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $productManufacturer = $this->ProductManufacturers->patchEntity(
                $productManufacturer,
                $this->getRequest()->getdata()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.beforeEdit', $this, [
                'ProductManufacturer' => $productManufacturer,
            ]);
            if ($this->ProductManufacturers->save($productManufacturer)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.onEditSuccess', $this, [
                    'ProductManufacturer' => $productManufacturer,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product manufacturer has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.onEditFailure', $this, [
                    'ProductManufacturer' => $productManufacturer,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product manufacturer could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.beforeEditRender', $this, [
            'ProductManufacturer' => $productManufacturer,
        ]);

        $this->set('productManufacturer', $productManufacturer);
    }

    /**
     * Delete method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $productManufacturer = $this->ProductManufacturers->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.beforeDelete', $this, [
            'ProductManufacturer' => $productManufacturer,
        ]);
        if ($this->ProductManufacturers->delete($productManufacturer)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.onDeleteSuccess', $this, [
                'ProductManufacturer' => $productManufacturer,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product manufacturer has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductManufacturers.onDeleteFailure', $this, [
                'ProductManufacturer' => $productManufacturer,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product manufacturer could not be deleted. Please, try again.'),
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
                $productManufacturers = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($productManufacturers) && !empty($productManufacturers)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($productManufacturers[0])) {
                    $headerArray = $this->ProductManufacturers->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($productManufacturers[0]));
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
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $productManufacturers);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($productManufacturers as $productManufacturer) {
                    $dateTime = DateTime::now();
                    $existent = $this->ProductManufacturers
                        ->find('all')
                        ->where([
                            'name' => $productManufacturer['name'],
                            'slug' => $productManufacturer['slug'],
                        ])
                        ->first();
                    if (empty($existent)) {
                        $entity = $this->ProductManufacturers->newEmptyEntity(); // create
                        $productManufacturer = $this->ProductManufacturers->patchEntity(
                            $entity,
                            Hash::merge(
                                $productManufacturer,
                                [
                                    'slug'      => Text::slug(strtolower($productManufacturer['slug'])),
                                    'created'   => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified'  => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->ProductManufacturers->save($productManufacturer)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->ProductManufacturers->get($existent->id); // update
                        $productManufacturer = $this->ProductManufacturers->patchEntity(
                            $existent,
                            Hash::merge(
                                $productManufacturer,
                                [
                                    'slug'      => Text::slug(strtolower($productManufacturer['slug'])),
                                    'modified'  => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->ProductManufacturers->save($productManufacturer)) {
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
        $productManufacturers = $this->ProductManufacturers->find('all');
        $header = $this->ProductManufacturers->tableColumns;

        $productManufacturersArray = [];
        foreach($productManufacturers as $productManufacturer) {
            $productManufacturerArray = [];
            $productManufacturerArray['id'] = $productManufacturer->id;
            $productManufacturerArray['foreign_key'] = $productManufacturer->foreign_key;
            $productManufacturerArray['name'] = $productManufacturer->name;
            $productManufacturerArray['slug'] = $productManufacturer->slug;
            $productManufacturerArray['website'] = $productManufacturer->website;
            $productManufacturerArray['description'] = $productManufacturer->description;
            $productManufacturerArray['logo'] = $productManufacturer->logo;
            $productManufacturerArray['image'] = $productManufacturer->image;
            $productManufacturerArray['status'] = ($productManufacturer->status == 1)? 1: 0;
            $productManufacturerArray['created'] = empty($productManufacturer->created)? NULL: $productManufacturer->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productManufacturerArray['modified'] = empty($productManufacturer->modified)? NULL: $productManufacturer->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productManufacturersArray[] = $productManufacturerArray;
        }
        $productManufacturers = $productManufacturersArray;

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
        foreach ($productManufacturers as $dataEntity) {
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
        $productManufacturers = $this->ProductManufacturers->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->ProductManufacturers->tableColumns;
        $extract = [
            'id',
            'foreign_key',
            'name',
            'slug',
            'website',
            'description',
            'logo',
            'image',
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
        $this->set(compact('productManufacturers'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'productManufacturers',
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
        $productManufacturers = $this->ProductManufacturers->find('all');

        $productManufacturersArray = [];
        foreach($productManufacturers as $productManufacturer) {
            $productManufacturerArray = [];
            $productManufacturerArray['id'] = $productManufacturer->id;
            $productManufacturerArray['foreign_key'] = $productManufacturer->foreign_key;
            $productManufacturerArray['name'] = $productManufacturer->name;
            $productManufacturerArray['slug'] = $productManufacturer->slug;
            $productManufacturerArray['website'] = $productManufacturer->website;
            $productManufacturerArray['description'] = $productManufacturer->description;
            $productManufacturerArray['logo'] = $productManufacturer->logo;
            $productManufacturerArray['image'] = $productManufacturer->image;
            $productManufacturerArray['status'] = ($productManufacturer->status == 1)? 1: 0;
            $productManufacturerArray['created'] = empty($productManufacturer->created)? NULL: $productManufacturer->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productManufacturerArray['modified'] = empty($productManufacturer->modified)? NULL: $productManufacturer->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productManufacturersArray[] = $productManufacturerArray;
        }
        $productManufacturers = ['ProductManufacturers' => ['ProductManufacturer' => $productManufacturersArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('productManufacturers'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'productManufacturers']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $productManufacturers = $this->ProductManufacturers->find('all');

        $productManufacturersArray = [];
        foreach($productManufacturers as $productManufacturer) {
            $productManufacturerArray = [];
            $productManufacturerArray['id'] = $productManufacturer->id;
            $productManufacturerArray['foreign_key'] = $productManufacturer->foreign_key;
            $productManufacturerArray['name'] = $productManufacturer->name;
            $productManufacturerArray['slug'] = $productManufacturer->slug;
            $productManufacturerArray['website'] = $productManufacturer->website;
            $productManufacturerArray['description'] = $productManufacturer->description;
            $productManufacturerArray['logo'] = $productManufacturer->logo;
            $productManufacturerArray['image'] = $productManufacturer->image;
            $productManufacturerArray['status'] = ($productManufacturer->status == 1)? 1: 0;
            $productManufacturerArray['created'] = empty($productManufacturer->created)? NULL: $productManufacturer->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productManufacturerArray['modified'] = empty($productManufacturer->modified)? NULL: $productManufacturer->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productManufacturersArray[] = $productManufacturerArray;
        }
        $productManufacturers = ['ProductManufacturers' => ['ProductManufacturer' => $productManufacturersArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('productManufacturers'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'productManufacturers']);
    }
}
