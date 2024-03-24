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
 * Domains Controller
 *
 * @property \BechlemConnectLight\Model\Table\DomainsTable $Domains
 */
class DomainsController extends AppController
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
        'sortableFields' => [
            'id',
            'scheme',
            'url',
            'name',
            'theme',
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
        $this->locale = $session->check('Locale.code')? $session->read('Locale.code'): 'en_US';
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->Domains
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.beforeIndexRender', $this, ['Query' => $query]);

        $this->set('domains', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id Domain id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $id = null)
    {
        $domain = $this->Domains->get($id, contain: [
            'Locales' => function ($q) {
                return $q->orderBy(['Locales.weight' => 'ASC']);
            }
        ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.beforeViewRender', $this, ['Domain' => $domain]);

        $this->set('domain', $domain);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $domain = $this->Domains->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $domain = $this->Domains->patchEntity($domain, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.beforeAdd', $this, ['Domain' => $domain]);
            if ($this->Domains->save($domain)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.onAddSuccess', $this, ['Domain' => $domain]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The domain has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.onAddFailure', $this, ['Domain' => $domain]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The domain could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $locales = $this->Domains->Locales
            ->find('list',
                order: ['Locales.weight' => 'ASC'],
                keyField: 'id',
                valueField: 'name'
            );

        BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.beforeAddRender', $this, [
            'Domain' => $domain,
            'Locales' => $locales,
        ]);

        $this->set(compact('domain', 'locales'));
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
        $domain = $this->Domains->get($id, contain: [
            'Locales' => function ($q) {
                return $q->orderBy(['Locales.weight' => 'ASC']);
            }
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $domain = $this->Domains->patchEntity($domain, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.beforeEdit', $this, ['Domain' => $domain]);
            if ($this->Domains->save($domain)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.onEditSuccess', $this, ['Domain' => $domain]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The domain has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]);

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.onEditFailure', $this, ['Domain' => $domain]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The domain could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]);
            }
        }

        $locales = $this->Domains->Locales
            ->find('list',
                order: ['Locales.weight' => 'ASC'],
                keyField: 'id',
                valueField: 'name'
            );

        BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.beforeEditRender', $this, [
            'Domain' => $domain,
            'Locales' => $locales,
        ]);

        $this->set(compact('domain', 'locales'));
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
        $domain = $this->Domains->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.beforeDelete', $this, ['Domain' => $domain]);
        if ($this->Domains->delete($domain)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.onDeleteSuccess', $this, ['Domain' => $domain]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The domain has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Domains.onDeleteFailure', $this, ['Domain' => $domain]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The domain could not be deleted. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
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
                $domains = $this->Global->csvToArray($targetPath, $del, $encl);
                if (is_array($domains) && !empty($domains)) {
                    unlink($targetPath);
                }

                // Check array keys
                if (!empty($domains[0])) {
                    $headerArray = $this->Domains->tableColumns;
                    $headerArrayDiff = array_diff($headerArray, array_keys($domains[0]));
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
                    $this->Global->logRequest($this, 'csvImport', $this->defaultTable, $domains);
                }

                $i = 0; // imported
                $u = 0; // updated

                foreach ($domains as $domain) {
                    $dateTime = DateTime::now();
                    $existent = $this->Domains
                        ->find('all')
                        ->where([
                            'url' => $domain['url'],
                            'name' => $domain['name'],
                            'theme' => $domain['theme']
                        ])
                        ->first();
                    if (empty($existent)) {
                        $entity = $this->Domains->newEmptyEntity(); // create
                        $domain = $this->Domains->patchEntity(
                            $entity,
                            Hash::merge(
                                $domain,
                                [
                                    'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ]
                            )
                        );
                        if ($this->Domains->save($domain)) {
                            $i++;
                        }
                    } else {
                        $existent = $this->Domains->get($existent->id); // update
                        $domain = $this->Domains->patchEntity(
                            $existent,
                            Hash::merge(
                                $domain,
                                ['modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss')]
                            )
                        );
                        if ($this->Domains->save($domain)) {
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
        $domains = $this->Domains->find('all');
        $header = $this->Domains->tableColumns;

        $domainsArray = [];
        foreach($domains as $domain) {
            $domainArray = [];
            $domainArray['id'] = $domain->id;
            $domainArray['scheme'] = $domain->scheme;
            $domainArray['url'] = $domain->url;
            $domainArray['name'] = $domain->name;
            $domainArray['theme'] = $domain->theme;
            $domainArray['created'] = empty($domain->created)? NULL: $domain->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $domainArray['modified'] = empty($domain->modified)? NULL: $domain->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $domainsArray[] = $domainArray;
        }
        $domains = $domainsArray;

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
        foreach ($domains as $dataEntity) {
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
        $domains = $this->Domains->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->Domains->tableColumns;
        $extract = [
            'id',
            'scheme',
            'url',
            'name',
            'theme',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('domains'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'domains',
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
        $domains = $this->Domains->find('all');

        $domainsArray = [];
        foreach($domains as $domain) {
            $domainArray = [];
            $domainArray['id'] = $domain->id;
            $domainArray['scheme'] = $domain->scheme;
            $domainArray['url'] = $domain->url;
            $domainArray['name'] = $domain->name;
            $domainArray['theme'] = $domain->theme;
            $domainArray['created'] = empty($domain->created)? NULL: $domain->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $domainArray['modified'] = empty($domain->modified)? NULL: $domain->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $domainsArray[] = $domainArray;
        }
        $domains = ['Domains' => ['Domain' => $domainsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('domains'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'domains']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $domains = $this->Domains->find('all');

        $domainsArray = [];
        foreach($domains as $domain) {
            $domainArray = [];
            $domainArray['id'] = $domain->id;
            $domainArray['scheme'] = $domain->scheme;
            $domainArray['url'] = $domain->url;
            $domainArray['name'] = $domain->name;
            $domainArray['theme'] = $domain->theme;
            $domainArray['created'] = empty($domain->created)? NULL: $domain->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $domainArray['modified'] = empty($domain->modified)? NULL: $domain->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $domainsArray[] = $domainArray;
        }
        $domains = ['Domains' => ['Domain' => $domainsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('domains'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'domains']);
    }
}
