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
 * BechlemSupplies Controller
 *
 * @property \BechlemConnectLight\Model\Table\BechlemSuppliesTable $BechlemSupplies
 */
class BechlemSuppliesController extends AppController
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
            'id_item',
            'id_brand',
            'brand',
            'art_nr',
            'part_nr',
            'name',
            'id_category',
            'category',
            'color',
            'is_compatible',
            've',
            'yield',
            'coverage',
            'measures',
            'content',
            'content_ml',
            'content_gram',
            'content_char',
            'german_group_no',
            'supply_series',
            'ean',
            'picture',
            'language',
            'created',
            'modified',
        ],
        'order' => ['name' => 'ASC']
    ];

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $query = $this->BechlemSupplies
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemSupplies.beforeIndexRender', $this, ['Query' => $query]);

        $this->set('bechlemSupplies', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id Bechlem Supply id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $id = null)
    {
        $bechlemSupply = $this->BechlemSupplies->get($id);

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemSupplies.beforeViewRender', $this, [
            'BechlemSupply' => $bechlemSupply
        ]);

        $this->set('bechlemSupply', $bechlemSupply);
    }

    /**
     * Update all method
     *
     * @return \Cake\Http\Response|null
     */
    public function updateAll()
    {
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            if ($this->BechlemSupplies->updateSupplies($this)) {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem supplies have been updated.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect($this->referer());
            } else {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem supplies could not be updated. Please, try again.'),
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
        $bechlemSupplies = $this->BechlemSupplies->find('all');
        $header = $this->BechlemSupplies->tableColumns;

        $bechlemSuppliesArray = [];
        foreach($bechlemSupplies as $bechlemSupply) {
            $bechlemSupplyArray = [];
            $bechlemSupplyArray['id'] = $bechlemSupply->id;
            $bechlemSupplyArray['id_item'] = $bechlemSupply->id_item;
            $bechlemSupplyArray['id_brand'] = $bechlemSupply->id_brand;
            $bechlemSupplyArray['brand'] = $bechlemSupply->brand;
            $bechlemSupplyArray['art_nr'] = $bechlemSupply->art_nr;
            $bechlemSupplyArray['part_nr'] = $bechlemSupply->part_nr;
            $bechlemSupplyArray['name'] = $bechlemSupply->name;
            $bechlemSupplyArray['id_category'] = $bechlemSupply->id_category;
            $bechlemSupplyArray['category'] = $bechlemSupply->category;
            $bechlemSupplyArray['color'] = $bechlemSupply->color;
            $bechlemSupplyArray['is_compatible'] = $bechlemSupply->is_compatible;
            $bechlemSupplyArray['ve'] = $bechlemSupply->ve;
            $bechlemSupplyArray['yield'] = $bechlemSupply->yield;
            $bechlemSupplyArray['coverage'] = $bechlemSupply->coverage;
            $bechlemSupplyArray['measures'] = $bechlemSupply->measures;
            $bechlemSupplyArray['content'] = $bechlemSupply->content;
            $bechlemSupplyArray['content_ml'] = $bechlemSupply->content_ml;
            $bechlemSupplyArray['content_gram'] = $bechlemSupply->content_gram;
            $bechlemSupplyArray['content_char'] = $bechlemSupply->content_char;
            $bechlemSupplyArray['german_group_no'] = $bechlemSupply->german_group_no;
            $bechlemSupplyArray['supply_series'] = $bechlemSupply->supply_series;
            $bechlemSupplyArray['ean'] = $bechlemSupply->ean;
            $bechlemSupplyArray['picture'] = $bechlemSupply->picture;
            $bechlemSupplyArray['language'] = $bechlemSupply->language;
            $bechlemSupplyArray['created'] = empty($bechlemSupply->created)? NULL: $bechlemSupply->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemSupplyArray['modified'] = empty($bechlemSupply->modified)? NULL: $bechlemSupply->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemSuppliesArray[] = $bechlemSupplyArray;
        }
        $bechlemSupplies = $bechlemSuppliesArray;

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
        foreach ($bechlemSupplies as $dataEntity) {
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
        $bechlemSupplies = $this->BechlemSupplies->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->BechlemSupplies->tableColumns;
        $extract = [
            'id',
            'id_item',
            'id_brand',
            'brand',
            'art_nr',
            'part_nr',
            'name',
            'id_category',
            'category',
            'color',
            'is_compatible',
            've',
            'yield',
            'coverage',
            'measures',
            'content',
            'content_ml',
            'content_gram',
            'content_char',
            'german_group_no',
            'supply_series',
            'ean',
            'picture',
            'language',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('bechlemSupplies'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'bechlemSupplies',
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
        $bechlemSupplies = $this->BechlemSupplies->find('all');

        $bechlemSuppliesArray = [];
        foreach($bechlemSupplies as $bechlemSupply) {
            $bechlemSupplyArray = [];
            $bechlemSupplyArray['id'] = $bechlemSupply->id;
            $bechlemSupplyArray['id_item'] = $bechlemSupply->id_item;
            $bechlemSupplyArray['id_brand'] = $bechlemSupply->id_brand;
            $bechlemSupplyArray['brand'] = $bechlemSupply->brand;
            $bechlemSupplyArray['art_nr'] = $bechlemSupply->art_nr;
            $bechlemSupplyArray['part_nr'] = $bechlemSupply->part_nr;
            $bechlemSupplyArray['name'] = $bechlemSupply->name;
            $bechlemSupplyArray['id_category'] = $bechlemSupply->id_category;
            $bechlemSupplyArray['category'] = $bechlemSupply->category;
            $bechlemSupplyArray['color'] = $bechlemSupply->color;
            $bechlemSupplyArray['is_compatible'] = $bechlemSupply->is_compatible;
            $bechlemSupplyArray['ve'] = $bechlemSupply->ve;
            $bechlemSupplyArray['yield'] = $bechlemSupply->yield;
            $bechlemSupplyArray['coverage'] = $bechlemSupply->coverage;
            $bechlemSupplyArray['measures'] = $bechlemSupply->measures;
            $bechlemSupplyArray['content'] = $bechlemSupply->content;
            $bechlemSupplyArray['content_ml'] = $bechlemSupply->content_ml;
            $bechlemSupplyArray['content_gram'] = $bechlemSupply->content_gram;
            $bechlemSupplyArray['content_char'] = $bechlemSupply->content_char;
            $bechlemSupplyArray['german_group_no'] = $bechlemSupply->german_group_no;
            $bechlemSupplyArray['supply_series'] = $bechlemSupply->supply_series;
            $bechlemSupplyArray['ean'] = $bechlemSupply->ean;
            $bechlemSupplyArray['picture'] = $bechlemSupply->picture;
            $bechlemSupplyArray['language'] = $bechlemSupply->language;
            $bechlemSupplyArray['created'] = empty($bechlemSupply->created)? NULL: $bechlemSupply->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemSupplyArray['modified'] = empty($bechlemSupply->modified)? NULL: $bechlemSupply->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemSuppliesArray[] = $bechlemSupplyArray;
        }
        $bechlemSupplies = ['BechlemSupplies' => ['BechlemSupply' => $bechlemSuppliesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('bechlemSupplies'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'bechlemSupplies']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $bechlemSupplies = $this->BechlemSupplies->find('all');

        $bechlemSuppliesArray = [];
        foreach($bechlemSupplies as $bechlemSupply) {
            $bechlemSupplyArray = [];
            $bechlemSupplyArray['id'] = $bechlemSupply->id;
            $bechlemSupplyArray['id_item'] = $bechlemSupply->id_item;
            $bechlemSupplyArray['id_brand'] = $bechlemSupply->id_brand;
            $bechlemSupplyArray['brand'] = $bechlemSupply->brand;
            $bechlemSupplyArray['art_nr'] = $bechlemSupply->art_nr;
            $bechlemSupplyArray['part_nr'] = $bechlemSupply->part_nr;
            $bechlemSupplyArray['name'] = $bechlemSupply->name;
            $bechlemSupplyArray['id_category'] = $bechlemSupply->id_category;
            $bechlemSupplyArray['category'] = $bechlemSupply->category;
            $bechlemSupplyArray['color'] = $bechlemSupply->color;
            $bechlemSupplyArray['is_compatible'] = $bechlemSupply->is_compatible;
            $bechlemSupplyArray['ve'] = $bechlemSupply->ve;
            $bechlemSupplyArray['yield'] = $bechlemSupply->yield;
            $bechlemSupplyArray['coverage'] = $bechlemSupply->coverage;
            $bechlemSupplyArray['measures'] = $bechlemSupply->measures;
            $bechlemSupplyArray['content'] = $bechlemSupply->content;
            $bechlemSupplyArray['content_ml'] = $bechlemSupply->content_ml;
            $bechlemSupplyArray['content_gram'] = $bechlemSupply->content_gram;
            $bechlemSupplyArray['content_char'] = $bechlemSupply->content_char;
            $bechlemSupplyArray['german_group_no'] = $bechlemSupply->german_group_no;
            $bechlemSupplyArray['supply_series'] = $bechlemSupply->supply_series;
            $bechlemSupplyArray['ean'] = $bechlemSupply->ean;
            $bechlemSupplyArray['picture'] = $bechlemSupply->picture;
            $bechlemSupplyArray['language'] = $bechlemSupply->language;
            $bechlemSupplyArray['created'] = empty($bechlemSupply->created)? NULL: $bechlemSupply->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemSupplyArray['modified'] = empty($bechlemSupply->modified)? NULL: $bechlemSupply->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemSuppliesArray[] = $bechlemSupplyArray;
        }
        $bechlemSupplies = ['BechlemSupplies' => ['BechlemSupply' => $bechlemSuppliesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('bechlemSupplies'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'bechlemSupplies']);
    }
}
