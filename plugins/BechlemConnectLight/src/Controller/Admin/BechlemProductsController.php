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
use Cake\Http\CallbackStream;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * BechlemProducts Controller
 *
 * @property \BechlemConnectLight\Model\Table\BechlemProductsTable $BechlemProducts
 */
class BechlemProductsController extends AppController
{

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
            'bechlem_id',
            'ean',
            'manufacturer_sku',
            'your_sku',
            'manufacturer_id',
            'manufacturer_name',
            'product_name_with_manufacturer',
            'short_description',
            'product_type_id',
            'product_type_name',
            'image',
            'created',
            'modified',
        ],
        'order' => ['bechlem_id' => 'ASC']
    ];

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $query = $this->BechlemProducts
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemProducts.beforeIndexRender', $this, ['Query' => $query]);

        $this->set('bechlemProducts', $this->paginate($query));
    }

    /**
     * Index cards method
     *
     * @return void
     */
    public function indexCards()
    {
        $query = $this->BechlemProducts
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemProducts.beforeIndexRender', $this, ['Query' => $query]);

        $this->set('bechlemProducts', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id Bechlem Product id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $id = null)
    {
        $bechlemProduct = $this->BechlemProducts->get($id, contain: [
            'BechlemProductAccessories.BechlemProducts',
            'BechlemProductAccessories.BechlemPrinters',
            'BechlemProductAccessories.BechlemSupplies',
        ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemProducts.beforeViewRender', $this, [
            'BechlemProduct' => $bechlemProduct
        ]);

        $this->set('bechlemProduct', $bechlemProduct);
    }

    /**
     * Update all method
     *
     * @return \Cake\Http\Response|null
     */
    public function updateAll()
    {
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {

            $BechlemBrands = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemBrands');
            $BechlemCategories = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemCategories');
            $BechlemIdentifiers = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemIdentifiers');
            $BechlemPrinters = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemPrinters');
            $BechlemPrinterSerieses = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemPrinterSerieses');
            $BechlemPrinterToSupplies = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemPrinterToSupplies');
            $BechlemResellerItems = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemResellerItems');
            $BechlemResellers = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemResellers');
            $BechlemSupplies = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemSupplies');
            $BechlemSupplySerieses = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemSupplySerieses');
            $BechlemSupplyToOemReferences = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemSupplyToOemReferences');
            $BechlemSupplyToSupplies = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemSupplyToSupplies');
            $BechlemProductAccessories = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemProductAccessories');

            if (
                $BechlemBrands->updateBrands($this) && 
                $BechlemCategories->updateCategories($this) &&
                $BechlemIdentifiers->updateIdentifiers($this) &&
                $BechlemPrinters->updatePrinters($this) &&
                $BechlemPrinterSerieses->updatePrinterSerieses($this) &&
                $BechlemPrinterToSupplies->updatePrinterToSupplies($this) &&
                $BechlemResellerItems->updateResellerItems($this) &&
                $BechlemResellers->updateResellers($this) &&
                $BechlemSupplies->updateSupplies($this) &&
                $BechlemSupplySerieses->updateSupplySerieses($this) &&
                $BechlemSupplyToOemReferences->updateSupplyToOemReferences($this) &&
                $BechlemSupplyToSupplies->updateSupplyToSupplies($this) &&
                $BechlemProductAccessories->updateProductAccessories($this) &&
                $this->BechlemProducts->updateProducts($this)
            ) {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem products have been updated.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect($this->referer());
            } else {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem products could not be updated. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        return $this->redirect($this->referer());
    }

    /**
     * Load data method
     *
     * @return \Cake\Http\Response|null|static
     */
    public function loadData()
    {
        // Allowed request method is ajax
        $this->getRequest()->allowMethod(['ajax']);
        $this->autoRender = false;

        $product = [];
        if ($this->getRequest()->is('ajax') && !is_null($this->getRequest()->getQuery())) {

            $key = null;
            if (!empty($this->getRequest()->getQuery('key'))) {
                $key = $this->getRequest()->getQuery('key');
            }

            // Check if key is a EAN
            if (
                is_numeric($key) &&
                preg_match('/^[0-9]+$/', (string)$key) &&
                (strlen((string)$key) >= 12)
            ) {
                $bechlemProduct = $this->BechlemProducts
                    ->find()
                    ->where(['BechlemProducts.ean LIKE' => '%' . $key . '%'])
                    ->first();
                if (!empty($bechlemProduct->id)) {
                    $this->response->getBody()->write(json_encode(['product' => $bechlemProduct]));
                    $this->response = $this->response->withType('json');
                    return $this->response;
                }
            }

            // Check if key is a Bechlem Id
            if (
                is_numeric($key) &&
                preg_match('/^[0-9]+$/', (string)$key) &&
                (strlen((string)$key) <= 11)
            ) {
                $bechlemProduct = $this->BechlemProducts
                    ->find()
                    ->where(['BechlemProducts.bechlem_id' => $key])
                    ->first();
                if (!empty($bechlemProduct->id)) {
                    $this->response->getBody()->write(json_encode(['product' => $bechlemProduct]));
                    $this->response = $this->response->withType('json');
                    return $this->response;
                }
            }

            // Check if search query is a Manufacturer SKU
            if (!empty($key) && !preg_match('/\s/', $key)) {
                $bechlemProduct = $this->BechlemProducts
                    ->find()
                    ->where(['BechlemProducts.manufacturer_sku LIKE' => '%' . $key . '%'])
                    ->first();
                if (!empty($bechlemProduct->id)) {
                    $this->response->getBody()->write(json_encode(['product' => $bechlemProduct]));
                    $this->response = $this->response->withType('json');
                    return $this->response;
                }
            }

            $this->response->getBody()->write(json_encode($product));
            $this->response = $this->response->withType('json');
            return $this->response;

        }

        $this->response->getBody()->write(json_encode($product));
        $this->response = $this->response->withType('json');
        return $this->response;
    }

    /**
     * Export xlsx method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportXlsx()
    {
        $bechlemProducts = $this->BechlemProducts->find('all');
        $header = $this->BechlemProducts->tableColumns;

        $bechlemProductsArray = [];
        foreach($bechlemProducts as $bechlemProduct) {
            $bechlemProductArray = [];
            $bechlemProductArray['id'] = $bechlemProduct->id;
            $bechlemProductArray['bechlem_id'] = $bechlemProduct->bechlem_id;
            $bechlemProductArray['ean'] = $bechlemProduct->ean;
            $bechlemProductArray['manufacturer_sku'] = $bechlemProduct->manufacturer_sku;
            $bechlemProductArray['your_sku'] = $bechlemProduct->your_sku;
            $bechlemProductArray['manufacturer_id'] = $bechlemProduct->manufacturer_id;
            $bechlemProductArray['manufacturer_name'] = $bechlemProduct->manufacturer_name;
            $bechlemProductArray['product_name_with_manufacturer'] = $bechlemProduct->product_name_with_manufacturer;
            $bechlemProductArray['short_description'] = $bechlemProduct->short_description;
            $bechlemProductArray['product_type_id'] = $bechlemProduct->product_type_id;
            $bechlemProductArray['product_type_name'] = $bechlemProduct->product_type_name;
            $bechlemProductArray['image'] = $bechlemProduct->image;
            $bechlemProductArray['created'] = empty($bechlemProduct->created)? NULL: $bechlemProduct->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemProductArray['modified'] = empty($bechlemProduct->modified)? NULL: $bechlemProduct->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemProductsArray[] = $bechlemProductArray;
        }
        $bechlemProducts = $bechlemProductsArray;

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
        foreach ($bechlemProducts as $dataEntity) {
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
        $bechlemProducts = $this->BechlemProducts->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->BechlemProducts->tableColumns;
        $extract = [
            'id',
            'bechlem_id',
            'ean',
            'manufacturer_sku',
            'your_sku',
            'manufacturer_id',
            'manufacturer_name',
            'product_name_with_manufacturer',
            'short_description',
            'product_type_id',
            'product_type_name',
            'image',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('bechlemProducts'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'bechlemProducts',
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
        $bechlemProducts = $this->BechlemProducts->find('all');

        $bechlemProductsArray = [];
        foreach($bechlemProducts as $bechlemProduct) {
            $bechlemProductArray = [];
            $bechlemProductArray['id'] = $bechlemProduct->id;
            $bechlemProductArray['bechlem_id'] = $bechlemProduct->bechlem_id;
            $bechlemProductArray['ean'] = $bechlemProduct->ean;
            $bechlemProductArray['manufacturer_sku'] = $bechlemProduct->manufacturer_sku;
            $bechlemProductArray['your_sku'] = $bechlemProduct->your_sku;
            $bechlemProductArray['manufacturer_id'] = $bechlemProduct->manufacturer_id;
            $bechlemProductArray['manufacturer_name'] = $bechlemProduct->manufacturer_name;
            $bechlemProductArray['product_name_with_manufacturer'] = $bechlemProduct->product_name_with_manufacturer;
            $bechlemProductArray['short_description'] = $bechlemProduct->short_description;
            $bechlemProductArray['product_type_id'] = $bechlemProduct->product_type_id;
            $bechlemProductArray['product_type_name'] = $bechlemProduct->product_type_name;
            $bechlemProductArray['image'] = $bechlemProduct->image;
            $bechlemProductArray['created'] = empty($bechlemProduct->created)? NULL: $bechlemProduct->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemProductArray['modified'] = empty($bechlemProduct->modified)? NULL: $bechlemProduct->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemProductsArray[] = $bechlemProductArray;
        }
        $bechlemProducts = ['BechlemProducts' => ['BechlemProduct' => $bechlemProductsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('bechlemProducts'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'bechlemProducts']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $bechlemProducts = $this->BechlemProducts->find('all');

        $bechlemProductsArray = [];
        foreach($bechlemProducts as $bechlemProduct) {
            $bechlemProductArray = [];
            $bechlemProductArray['id'] = $bechlemProduct->id;
            $bechlemProductArray['bechlem_id'] = $bechlemProduct->bechlem_id;
            $bechlemProductArray['ean'] = $bechlemProduct->ean;
            $bechlemProductArray['manufacturer_sku'] = $bechlemProduct->manufacturer_sku;
            $bechlemProductArray['your_sku'] = $bechlemProduct->your_sku;
            $bechlemProductArray['manufacturer_id'] = $bechlemProduct->manufacturer_id;
            $bechlemProductArray['manufacturer_name'] = $bechlemProduct->manufacturer_name;
            $bechlemProductArray['product_name_with_manufacturer'] = $bechlemProduct->product_name_with_manufacturer;
            $bechlemProductArray['short_description'] = $bechlemProduct->short_description;
            $bechlemProductArray['product_type_id'] = $bechlemProduct->product_type_id;
            $bechlemProductArray['product_type_name'] = $bechlemProduct->product_type_name;
            $bechlemProductArray['image'] = $bechlemProduct->image;
            $bechlemProductArray['created'] = empty($bechlemProduct->created)? NULL: $bechlemProduct->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemProductArray['modified'] = empty($bechlemProduct->modified)? NULL: $bechlemProduct->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemProductsArray[] = $bechlemProductArray;
        }
        $bechlemProducts = ['BechlemProducts' => ['BechlemProduct' => $bechlemProductsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('bechlemProducts'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'bechlemProducts']);
    }
}
