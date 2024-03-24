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
 * BechlemIdentifiers Controller
 *
 * @property \BechlemConnectLight\Model\Table\BechlemIdentifiersTable $BechlemIdentifiers
 */
class BechlemIdentifiersController extends AppController
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
            'id_position',
            'id_type',
            'id_identifier',
            'sync_id',
            'created',
            'modified',
        ],
        'order' => ['id_identifier' => 'ASC']
    ];

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $query = $this->BechlemIdentifiers
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemIdentifiers.beforeIndexRender', $this, ['Query' => $query]);

        $this->set('bechlemIdentifiers', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id Bechlem Identifier id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $id = null)
    {
        $bechlemIdentifier = $this->BechlemIdentifiers->get($id);

        BechlemConnectLight::dispatchEvent('Controller.Admin.BechlemIdentifiers.beforeViewRender', $this, [
            'BechlemIdentifier' => $bechlemIdentifier
        ]);

        $this->set('bechlemIdentifier', $bechlemIdentifier);
    }

    /**
     * Update all method
     *
     * @return \Cake\Http\Response|null
     */
    public function updateAll()
    {
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            if ($this->BechlemIdentifiers->updateIdentifiers()) {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem identifiers have been updated.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect($this->referer());
            } else {
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The Bechlem identifiers could not be updated. Please, try again.'),
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
        $bechlemIdentifiers = $this->BechlemIdentifiers->find('all');
        $header = $this->BechlemIdentifiers->tableColumns;

        $bechlemIdentifiersArray = [];
        foreach($bechlemIdentifiers as $bechlemIdentifier) {
            $bechlemIdentifierArray = [];
            $bechlemIdentifierArray['id'] = $bechlemIdentifier->id;
            $bechlemIdentifierArray['id_item'] = $bechlemIdentifier->id_item;
            $bechlemIdentifierArray['id_position'] = $bechlemIdentifier->id_position;
            $bechlemIdentifierArray['id_type'] = $bechlemIdentifier->id_type;
            $bechlemIdentifierArray['id_identifier'] = $bechlemIdentifier->id_identifier;
            $bechlemIdentifierArray['sync_id'] = $bechlemIdentifier->sync_id;
            $bechlemIdentifierArray['created'] = empty($bechlemIdentifier->created)? NULL: $bechlemIdentifier->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemIdentifierArray['modified'] = empty($bechlemIdentifier->modified)? NULL: $bechlemIdentifier->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $bechlemIdentifiersArray[] = $bechlemIdentifierArray;
        }
        $bechlemIdentifiers = $bechlemIdentifiersArray;

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
        foreach ($bechlemIdentifiers as $dataEntity) {
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
        $bechlemIdentifiers = $this->BechlemIdentifiers->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->BechlemIdentifiers->tableColumns;
        $extract = [
            'id',
            'id_item',
            'id_position',
            'id_type',
            'id_identifier',
            'sync_id',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('bechlemIdentifiers'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'bechlemIdentifiers',
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
        $bechlemIdentifiers = $this->BechlemIdentifiers->find('all');

        $bechlemIdentifiersArray = [];
        foreach($bechlemIdentifiers as $bechlemIdentifier) {
            $bechlemIdentifierArray = [];
            $bechlemIdentifierArray['id'] = $bechlemIdentifier->id;
            $bechlemIdentifierArray['id_item'] = $bechlemIdentifier->id_item;
            $bechlemIdentifierArray['id_position'] = $bechlemIdentifier->id_position;
            $bechlemIdentifierArray['id_type'] = $bechlemIdentifier->id_type;
            $bechlemIdentifierArray['id_identifier'] = $bechlemIdentifier->id_identifier;
            $bechlemIdentifierArray['sync_id'] = $bechlemIdentifier->sync_id;
            $bechlemIdentifierArray['created'] = empty($bechlemIdentifier->created)? NULL: $bechlemIdentifier->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemIdentifierArray['modified'] = empty($bechlemIdentifier->modified)? NULL: $bechlemIdentifier->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $bechlemIdentifiersArray[] = $bechlemIdentifierArray;
        }
        $bechlemIdentifiers = ['BechlemIdentifiers' => ['BechlemIdentifier' => $bechlemIdentifiersArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('bechlemIdentifiers'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'bechlemIdentifiers']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $bechlemIdentifiers = $this->BechlemIdentifiers->find('all');

        $bechlemIdentifiersArray = [];
        foreach($bechlemIdentifiers as $bechlemIdentifier) {
            $bechlemIdentifierArray = [];
            $bechlemIdentifierArray['id'] = $bechlemIdentifier->id;
            $bechlemIdentifierArray['id_item'] = $bechlemIdentifier->id_item;
            $bechlemIdentifierArray['id_position'] = $bechlemIdentifier->id_position;
            $bechlemIdentifierArray['id_type'] = $bechlemIdentifier->id_type;
            $bechlemIdentifierArray['id_identifier'] = $bechlemIdentifier->id_identifier;
            $bechlemIdentifierArray['sync_id'] = $bechlemIdentifier->sync_id;
            $bechlemIdentifierArray['created'] = empty($bechlemIdentifier->created)? NULL: $bechlemIdentifier->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $bechlemIdentifierArray['modified'] = empty($bechlemIdentifier->modified)? NULL: $bechlemIdentifier->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $bechlemIdentifiersArray[] = $bechlemIdentifierArray;
        }
        $bechlemIdentifiers = ['BechlemIdentifiers' => ['BechlemIdentifier' => $bechlemIdentifiersArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('bechlemIdentifiers'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'bechlemIdentifiers']);
    }
}
