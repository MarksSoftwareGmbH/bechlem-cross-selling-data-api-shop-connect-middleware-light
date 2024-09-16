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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * BechlemResellerItems Controller
 *
 * @property \BechlemConnectLight\Model\Table\BechlemResellerItemsTable $BechlemResellerItems
 */
class BechlemResellerItemsController extends AppController
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
            'id_art_nr',
            'id_reseller',
            'id_item',
            'ean',
            'oem_nr',
            'description',
            've',
            'language',
            'created',
            'modified',
        ],
        'order' => ['oem_nr' => 'ASC']
    ];

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $query = $this->BechlemResellerItems
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemResellerItems.beforeIndexRender', $this, ['Query' => $query]);

        $this->set('bechlemResellerItems', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id Bechlem Reseller Item id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $id = null)
    {
        $bechlemResellerItem = $this->BechlemResellerItems->get($id);

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemResellerItems.beforeViewRender', $this, [
            'BechlemResellerItem' => $bechlemResellerItem
        ]);

        $this->set('bechlemResellerItem', $bechlemResellerItem);
    }

    /**
     * Update all method
     *
     * @return \Cake\Http\Response|null
     */
    public function updateAll()
    {
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            if ($this->BechlemResellerItems->updateResellerItems($this)) {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem reseller items have been updated.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect($this->referer());
            } else {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem reseller items could not be updated. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        return $this->redirect($this->referer());
    }

    /**
     * Export xlsx method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportXlsx()
    {
        $bechlemResellerItems = $this->BechlemResellerItems->find('all');
        $header = $this->BechlemResellerItems->tableColumns;

        $bechlemResellerItemsArray = [];
        foreach($bechlemResellerItems as $bechlemResellerItem) {
            $bechlemResellerItemArray = [];
            $bechlemResellerItemArray['id'] = $bechlemResellerItem->id;
            $bechlemResellerItemArray['id_art_nr'] = $bechlemResellerItem->id_art_nr;
            $bechlemResellerItemArray['id_reseller'] = $bechlemResellerItem->id_reseller;
            $bechlemResellerItemArray['id_item'] = $bechlemResellerItem->id_item;
            $bechlemResellerItemArray['ean'] = $bechlemResellerItem->ean;
            $bechlemResellerItemArray['oem_nr'] = $bechlemResellerItem->oem_nr;
            $bechlemResellerItemArray['description'] = $bechlemResellerItem->description;
            $bechlemResellerItemArray['ve'] = $bechlemResellerItem->ve;
            $bechlemResellerItemArray['language'] = $bechlemResellerItem->language;
            $bechlemResellerItemArray['created'] = empty($bechlemResellerItem->created)? NULL: $bechlemResellerItem->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemResellerItemArray['modified'] = empty($bechlemResellerItem->modified)? NULL: $bechlemResellerItem->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemResellerItemsArray[] = $bechlemResellerItemArray;
        }
        $bechlemResellerItems = $bechlemResellerItemsArray;

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
        foreach ($bechlemResellerItems as $dataEntity) {
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
        $bechlemResellerItems = $this->BechlemResellerItems->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->BechlemResellerItems->tableColumns;
        $extract = [
            'id',
            'id_art_nr',
            'id_reseller',
            'id_item',
            'ean',
            'oem_nr',
            'description',
            've',
            'language',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('bechlemResellerItems'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'bechlemResellerItems',
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
        $bechlemResellerItems = $this->BechlemResellerItems->find('all');

        $bechlemResellerItemsArray = [];
        foreach($bechlemResellerItems as $bechlemResellerItem) {
            $bechlemResellerItemArray = [];
            $bechlemResellerItemArray['id'] = $bechlemResellerItem->id;
            $bechlemResellerItemArray['id_art_nr'] = $bechlemResellerItem->id_art_nr;
            $bechlemResellerItemArray['id_reseller'] = $bechlemResellerItem->id_reseller;
            $bechlemResellerItemArray['id_item'] = $bechlemResellerItem->id_item;
            $bechlemResellerItemArray['ean'] = $bechlemResellerItem->ean;
            $bechlemResellerItemArray['oem_nr'] = $bechlemResellerItem->oem_nr;
            $bechlemResellerItemArray['description'] = $bechlemResellerItem->description;
            $bechlemResellerItemArray['ve'] = $bechlemResellerItem->ve;
            $bechlemResellerItemArray['language'] = $bechlemResellerItem->language;
            $bechlemResellerItemArray['created'] = empty($bechlemResellerItem->created)? NULL: $bechlemResellerItem->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemResellerItemArray['modified'] = empty($bechlemResellerItem->modified)? NULL: $bechlemResellerItem->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemResellerItemsArray[] = $bechlemResellerItemArray;
        }
        $bechlemResellerItems = ['BechlemResellerItems' => ['BechlemResellerItem' => $bechlemResellerItemsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('bechlemResellerItems'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'bechlemResellerItems']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $bechlemResellerItems = $this->BechlemResellerItems->find('all');

        $bechlemResellerItemsArray = [];
        foreach($bechlemResellerItems as $bechlemResellerItem) {
            $bechlemResellerItemArray = [];
            $bechlemResellerItemArray['id'] = $bechlemResellerItem->id;
            $bechlemResellerItemArray['id_art_nr'] = $bechlemResellerItem->id_art_nr;
            $bechlemResellerItemArray['id_reseller'] = $bechlemResellerItem->id_reseller;
            $bechlemResellerItemArray['id_item'] = $bechlemResellerItem->id_item;
            $bechlemResellerItemArray['ean'] = $bechlemResellerItem->ean;
            $bechlemResellerItemArray['oem_nr'] = $bechlemResellerItem->oem_nr;
            $bechlemResellerItemArray['description'] = $bechlemResellerItem->description;
            $bechlemResellerItemArray['ve'] = $bechlemResellerItem->ve;
            $bechlemResellerItemArray['language'] = $bechlemResellerItem->language;
            $bechlemResellerItemArray['created'] = empty($bechlemResellerItem->created)? NULL: $bechlemResellerItem->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemResellerItemArray['modified'] = empty($bechlemResellerItem->modified)? NULL: $bechlemResellerItem->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemResellerItemsArray[] = $bechlemResellerItemArray;
        }
        $bechlemResellerItems = ['BechlemResellerItems' => ['BechlemResellerItem' => $bechlemResellerItemsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('bechlemResellerItems'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'bechlemResellerItems']);
    }
}
