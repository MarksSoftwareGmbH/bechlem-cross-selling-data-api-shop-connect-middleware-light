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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * ProductProductTypeAttributeValues Controller
 *
 * @property \BechlemConnectLight\Model\Table\ProductProductTypeAttributeValuesTable $ProductProductTypeAttributeValues
 */
class ProductProductTypeAttributeValuesController extends AppController
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
            'product_id',
            'product_type_attribute_id',
            'value',
            'created',
            'modified',
            'Products.name',
            'ProductTypeAttributes.alias',
        ],
        'order' => ['product_id' => 'ASC']
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
        $query = $this->ProductProductTypeAttributeValues
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->contain([
                'ProductTypeAttributes' => function ($q) {
                    return $q->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
                },
                'Products.ProductProductTypeAttributeValues.ProductTypeAttributes',
            ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.beforeIndexRender', $this, [
            'Query' => $query,
        ]);

        $this->set('productProductTypeAttributeValues', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $productProductTypeAttributeValue = $this->ProductProductTypeAttributeValues->get($id, contain: [
            'ProductTypeAttributes' => function ($q) {
                return $q->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
            },
            'Products.ProductProductTypeAttributeValues.ProductTypeAttributes',
        ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.beforeViewRender', $this, [
            'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
        ]);

        $this->set('productProductTypeAttributeValue', $productProductTypeAttributeValue);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $productProductTypeAttributeValue = $this->ProductProductTypeAttributeValues->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $productProductTypeAttributeValue = $this->ProductProductTypeAttributeValues->patchEntity(
                $productProductTypeAttributeValue,
                $this->getRequest()->getData()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.beforeAdd', $this, [
                'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
            ]);
            if ($this->ProductProductTypeAttributeValues->save($productProductTypeAttributeValue)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.onAddSuccess', $this, [
                    'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute value has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.onAddFailure', $this, [
                    'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute value could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $productTypeAttributes = $this->ProductProductTypeAttributeValues->ProductTypeAttributes
            ->find('list',
                order: ['ProductTypeAttributes.alias' => 'ASC'],
                keyField: 'id',
                valueField: 'title_alias'
            )
            ->limit(100);

        $products = $this->ProductProductTypeAttributeValues->Products
            ->find('list', keyField: 'id', valueField: 'name')
            ->contain(['ProductProductTypeAttributeValues.ProductTypeAttributes'])
            ->limit(100);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.beforeAddRender', $this, [
            'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
            'ProductTypeAttributes' => $productTypeAttributes,
            'Products' => $products,
        ]);

        $this->set(compact('productProductTypeAttributeValue', 'productTypeAttributes', 'products'));
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
        $productProductTypeAttributeValue = $this->ProductProductTypeAttributeValues->get($id);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $productProductTypeAttributeValue = $this->ProductProductTypeAttributeValues->patchEntity(
                $productProductTypeAttributeValue,
                $this->getRequest()->getData()
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.beforeEdit', $this, [
                'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
            ]);
            if ($this->ProductProductTypeAttributeValues->save($productProductTypeAttributeValue)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.onEditSuccess', $this, [
                    'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute value has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.onEditFailure', $this, [
                    'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product type attribute value could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $productTypeAttributes = $this->ProductProductTypeAttributeValues->ProductTypeAttributes
            ->find('list',
                order: ['ProductTypeAttributes.alias' => 'ASC'],
                keyField: 'id',
                valueField: 'title_alias'
            )
            ->limit(100);

        $products = $this->ProductProductTypeAttributeValues->Products
            ->find('list', keyField: 'id', valueField: 'name')
            ->contain(['ProductProductTypeAttributeValues.ProductTypeAttributes'])
            ->limit(100);

        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.beforeEditRender', $this, [
            'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
            'ProductTypeAttributes' => $productTypeAttributes,
            'Products' => $products,
        ]);

        $this->set(compact('productProductTypeAttributeValue', 'productTypeAttributes', 'products'));
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
        $productProductTypeAttributeValue = $this->ProductProductTypeAttributeValues->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.beforeDelete', $this, [
            'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
        ]);
        if ($this->ProductProductTypeAttributeValues->delete($productProductTypeAttributeValue)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.onDeleteSuccess', $this, [
                'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product type attribute value has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.ProductProductTypeAttributeValues.onDeleteFailure', $this, [
                'ProductProductTypeAttributeValue' => $productProductTypeAttributeValue,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product type attribute value could not be deleted. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Export xlsx method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportXlsx()
    {
        $productProductTypeAttributeValues = $this->ProductProductTypeAttributeValues->find('all');
        $header = $this->ProductProductTypeAttributeValues->tableColumns;

        $productProductTypeAttributeValuesArray = [];
        foreach($productProductTypeAttributeValues as $productProductTypeAttributeValue) {
            $productProductTypeAttributeValueArray = [];
            $productProductTypeAttributeValueArray['id'] = $productProductTypeAttributeValue->id;
            $productProductTypeAttributeValueArray['product_id'] = $productProductTypeAttributeValue->product_id;
            $productProductTypeAttributeValueArray['product_type_attribute_id'] = $productProductTypeAttributeValue->product_type_attribute_id;
            $productProductTypeAttributeValueArray['value'] = $productProductTypeAttributeValue->value;
            $productProductTypeAttributeValueArray['created'] = empty($productProductTypeAttributeValue->created)? NULL: $productProductTypeAttributeValue->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productProductTypeAttributeValueArray['modified'] = empty($productProductTypeAttributeValue->modified)? NULL: $productProductTypeAttributeValue->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productProductTypeAttributeValuesArray[] = $productProductTypeAttributeValueArray;
        }
        $productProductTypeAttributeValues = $productProductTypeAttributeValuesArray;

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
        foreach ($productProductTypeAttributeValues as $dataEntity) {
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
        $productProductTypeAttributeValues = $this->ProductProductTypeAttributeValues->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->ProductProductTypeAttributeValues->tableColumns;
        $extract = [
            'id',
            'product_id',
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
        $this->set(compact('productProductTypeAttributeValues'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'productProductTypeAttributeValues',
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
        $productProductTypeAttributeValues = $this->ProductProductTypeAttributeValues->find('all');

        $productProductTypeAttributeValuesArray = [];
        foreach($productProductTypeAttributeValues as $productProductTypeAttributeValue) {
            $productProductTypeAttributeValueArray = [];
            $productProductTypeAttributeValueArray['id'] = $productProductTypeAttributeValue->id;
            $productProductTypeAttributeValueArray['product_id'] = $productProductTypeAttributeValue->product_id;
            $productProductTypeAttributeValueArray['product_type_attribute_id'] = $productProductTypeAttributeValue->product_type_attribute_id;
            $productProductTypeAttributeValueArray['value'] = $productProductTypeAttributeValue->value;
            $productProductTypeAttributeValueArray['created'] = empty($productProductTypeAttributeValue->created)? NULL: $productProductTypeAttributeValue->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productProductTypeAttributeValueArray['modified'] = empty($productProductTypeAttributeValue->modified)? NULL: $productProductTypeAttributeValue->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productProductTypeAttributeValuesArray[] = $productProductTypeAttributeValueArray;
        }
        $productProductTypeAttributeValues = ['ProductProductTypeAttributeValues' => ['ProductProductTypeAttributeValue' => $productProductTypeAttributeValuesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('productProductTypeAttributeValues'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'productProductTypeAttributeValues']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $productProductTypeAttributeValues = $this->ProductProductTypeAttributeValues->find('all');

        $productProductTypeAttributeValuesArray = [];
        foreach($productProductTypeAttributeValues as $productProductTypeAttributeValue) {
            $productProductTypeAttributeValueArray = [];
            $productProductTypeAttributeValueArray['id'] = $productProductTypeAttributeValue->id;
            $productProductTypeAttributeValueArray['product_id'] = $productProductTypeAttributeValue->product_id;
            $productProductTypeAttributeValueArray['product_type_attribute_id'] = $productProductTypeAttributeValue->product_type_attribute_id;
            $productProductTypeAttributeValueArray['value'] = $productProductTypeAttributeValue->value;
            $productProductTypeAttributeValueArray['created'] = empty($productProductTypeAttributeValue->created)? NULL: $productProductTypeAttributeValue->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productProductTypeAttributeValueArray['modified'] = empty($productProductTypeAttributeValue->modified)? NULL: $productProductTypeAttributeValue->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $productProductTypeAttributeValuesArray[] = $productProductTypeAttributeValueArray;
        }
        $productProductTypeAttributeValues = ['ProductProductTypeAttributeValues' => ['ProductProductTypeAttributeValue' => $productProductTypeAttributeValuesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('productProductTypeAttributeValues'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'productProductTypeAttributeValues']);
    }
}
