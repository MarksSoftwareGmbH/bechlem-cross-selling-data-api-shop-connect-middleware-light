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
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Http\CallbackStream;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Logs Controller
 *
 * @property \BechlemConnectLight\Model\Table\LogsTable $Logs
 */
class LogsController extends AppController
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
            'request',
            'type',
            'message',
            'ip',
            'uri',
            'data',
            'created',
            'modified',
        ],
        'order' => ['created' => 'DESC']
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
        $this->locale = $session->check('Locale.code')? $session->read('Locale.code'): 'en_US';
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Logs
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->orderBy(['created' => 'desc']);

        BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.beforeIndexRender', $this, ['Query' => $query]);

        $this->set('logs', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $log = $this->Logs->get($id);

        BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.beforeViewRender', $this, ['Log' => $log]);

        $this->set('log', $log);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $log = $this->Logs->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $log = $this->Logs->patchEntity($log, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.beforeAdd', $this, ['Log' => $log]);
            if ($this->Logs->save($log)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.onAddSuccess', $this, ['Log' => $log]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The log has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.onAddFailure', $this, ['Log' => $log]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The log could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.beforeAddRender', $this, ['Log' => $log]);

        $this->set('log', $log);
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
        $log = $this->Logs->get($id);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $log = $this->Logs->patchEntity($log, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.beforeEdit', $this, ['Log' => $log]);
            if ($this->Logs->save($log)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.onEditSuccess', $this, ['Log' => $log]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The log has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.onEditFailure', $this, ['Log' => $log]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The log could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.beforeEditRender', $this, ['Log' => $log]);

        $this->set('log', $log);
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
        $log = $this->Logs->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.beforeDelete', $this, ['Log' => $log]);
        if ($this->Logs->delete($log)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.onDeleteSuccess', $this, ['Log' => $log]);

            $connection = ConnectionManager::get('default');
            $connection->delete('logs', ['id' => $id]);

            $this->Flash->set(
                __d('bechlem_connect_light', 'The log has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Logs.onDeleteFailure', $this, ['Log' => $log]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The log could not be deleted. Please, try again.'),
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
        $logs = $this->Logs->find('all');
        $header = $this->Logs->tableColumns;

        $logsArray = [];
        foreach($logs as $log) {
            $logArray = [];
            $logArray['id'] = $log->id;
            $logArray['request'] = $log->request;
            $logArray['type'] = $log->type;
            $logArray['message'] = $log->message;
            $logArray['ip'] = $log->ip;
            $logArray['uri'] = $log->uri;
            $logArray['data'] = $log->data;
            $logArray['created'] = $log->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $logArray['created_by'] = $log->created_by;

            $logsArray[] = $logArray;
        }
        $logs = $logsArray;

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
        foreach ($logs as $dataEntity) {
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
        $logs = $this->Logs->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->Logs->tableColumns;
        $extract = $this->Logs->tableColumns;
        $extract = [
            'id',
            'request',
            'type',
            'message',
            'ip',
            'uri',
            'data',
            function ($row) {
                return $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            'created_by',
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('logs'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'logs',
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
        $logs = $this->Logs->find('all');

        $logsArray = [];
        foreach($logs as $log) {
            $logArray = [];
            $logArray['id'] = $log->id;
            $logArray['request'] = $log->request;
            $logArray['type'] = $log->type;
            $logArray['message'] = $log->message;
            $logArray['ip'] = $log->ip;
            $logArray['uri'] = $log->uri;
            $logArray['data'] = $log->data;
            $logArray['created'] = $log->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $logArray['created_by'] = $log->created_by;

            $logsArray[] = $logArray;
        }
        $logs = ['Logs' => ['Log' => $logsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('logs'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'logs']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $logs = $this->Logs->find('all');

        $logsArray = [];
        foreach($logs as $log) {
            $logArray = [];
            $logArray['id'] = $log->id;
            $logArray['request'] = $log->request;
            $logArray['type'] = $log->type;
            $logArray['message'] = $log->message;
            $logArray['ip'] = $log->ip;
            $logArray['uri'] = $log->uri;
            $logArray['data'] = $log->data;
            $logArray['created'] = $log->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $logArray['created_by'] = $log->created_by;

            $logsArray[] = $logArray;
        }
        $logs = ['Logs' => ['Log' => $logsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('logs'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'logs']);
    }
}
