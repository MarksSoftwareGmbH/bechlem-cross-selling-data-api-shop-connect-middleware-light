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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * BechlemResellers Controller
 *
 * @property \BechlemConnectLight\Model\Table\BechlemResellersTable $BechlemResellers
 */
class BechlemResellersController extends AppController
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
            'id_reseller',
            'name',
            'picture',
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
        $query = $this->BechlemResellers
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemResellers.beforeIndexRender', $this, ['Query' => $query]);

        $this->set('bechlemResellers', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id Bechlem Reseller id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $id = null)
    {
        $bechlemReseller = $this->BechlemResellers->get($id);

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemResellers.beforeViewRender', $this, [
            'BechlemReseller' => $bechlemReseller
        ]);

        $this->set('bechlemReseller', $bechlemReseller);
    }

    /**
     * Update all method
     *
     * @return \Cake\Http\Response|null
     */
    public function updateAll()
    {
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            if ($this->BechlemResellers->updateResellers()) {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem resellers have been updated.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect($this->referer());
            } else {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem resellers could not be updated. Please, try again.'),
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
        $bechlemResellers = $this->BechlemResellers->find('all');
        $header = $this->BechlemResellers->tableColumns;

        $bechlemResellersArray = [];
        foreach($bechlemResellers as $bechlemReseller) {
            $bechlemResellerArray = [];
            $bechlemResellerArray['id'] = $bechlemReseller->id;
            $bechlemResellerArray['id_reseller'] = $bechlemReseller->id_reseller;
            $bechlemResellerArray['name'] = $bechlemReseller->name;
            $bechlemResellerArray['picture'] = $bechlemReseller->picture;
            $bechlemResellerArray['created'] = empty($bechlemReseller->created)? NULL: $bechlemReseller->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemResellerArray['modified'] = empty($bechlemReseller->modified)? NULL: $bechlemReseller->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemResellersArray[] = $bechlemResellerArray;
        }
        $bechlemResellers = $bechlemResellersArray;

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
        foreach ($bechlemResellers as $dataEntity) {
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
        $bechlemResellers = $this->BechlemResellers->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->BechlemResellers->tableColumns;
        $extract = [
            'id',
            'id_reseller',
            'name',
            'picture',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('bechlemResellers'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'bechlemResellers',
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
        $bechlemResellers = $this->BechlemResellers->find('all');

        $bechlemResellersArray = [];
        foreach($bechlemResellers as $bechlemReseller) {
            $bechlemResellerArray = [];
            $bechlemResellerArray['id'] = $bechlemReseller->id;
            $bechlemResellerArray['id_reseller'] = $bechlemReseller->id_reseller;
            $bechlemResellerArray['name'] = $bechlemReseller->name;
            $bechlemResellerArray['picture'] = $bechlemReseller->picture;
            $bechlemResellerArray['created'] = empty($bechlemReseller->created)? NULL: $bechlemReseller->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemResellerArray['modified'] = empty($bechlemReseller->modified)? NULL: $bechlemReseller->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemResellersArray[] = $bechlemResellerArray;
        }
        $bechlemResellers = ['BechlemResellers' => ['BechlemReseller' => $bechlemResellersArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('bechlemResellers'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'bechlemResellers']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $bechlemResellers = $this->BechlemResellers->find('all');

        $bechlemResellersArray = [];
        foreach($bechlemResellers as $bechlemReseller) {
            $bechlemResellerArray = [];
            $bechlemResellerArray['id'] = $bechlemReseller->id;
            $bechlemResellerArray['id_reseller'] = $bechlemReseller->id_reseller;
            $bechlemResellerArray['name'] = $bechlemReseller->name;
            $bechlemResellerArray['picture'] = $bechlemReseller->picture;
            $bechlemResellerArray['created'] = empty($bechlemReseller->created)? NULL: $bechlemReseller->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemResellerArray['modified'] = empty($bechlemReseller->modified)? NULL: $bechlemReseller->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $bechlemResellersArray[] = $bechlemResellerArray;
        }
        $bechlemResellers = ['BechlemResellers' => ['BechlemReseller' => $bechlemResellersArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('bechlemResellers'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'bechlemResellers']);
    }
}
