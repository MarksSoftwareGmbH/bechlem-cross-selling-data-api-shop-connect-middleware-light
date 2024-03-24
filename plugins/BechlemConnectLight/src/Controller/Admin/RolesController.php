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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Roles Controller
 *
 * @property \BechlemConnectLight\Model\Table\RolesTable $Roles
 */
class RolesController extends AppController
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
            'title',
            'alias',
            'created',
            'modified',
        ],
        'order' => ['title' => 'ASC']
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
        $query = $this->Roles
            ->find('search', search: $this->getRequest()->getQueryParams());

        BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.beforeIndexRender', $this, [
            'Query' => $query,
        ]);

        $this->set('roles', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $role = $this->Roles->get($id, contain: [
            'Users' => function ($q) {
                return $q->orderBy(['Users.username' => 'ASC']);
            }
        ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.beforeViewRender', $this, [
            'Role' => $role,
        ]);

        $this->set('role', $role);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $role = $this->Roles->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $role = $this->Roles->patchEntity($role, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.beforeAdd', $this, [
                'Role' => $role,
            ]);
            if ($this->Roles->save($role)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.onAddSuccess', $this, [
                    'Role' => $role,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The role has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.onAddFailure', $this, [
                    'Role' => $role,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The role could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $users = $this->Roles->Users->find('list', keyField: 'id', valueField: 'full_name_username');

        BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.beforeAddRender', $this, [
            'Role' => $role,
            'Users' => $users,
        ]);

        $this->set(compact('role', 'users'));
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
        $role = $this->Roles->get($id, contain: [
            'Users' => function ($q) {
                return $q->orderBy(['Users.username' => 'ASC']);
            }
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $role = $this->Roles->patchEntity($role, $this->getRequest()->getData());
            BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.beforeEdit', $this, [
                'Role' => $role,
            ]);
            if ($this->Roles->save($role)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.onEditSuccess', $this, [
                    'Role' => $role,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The role has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.onEditFailure', $this, [
                    'Role' => $role,
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The role could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $users = $this->Roles->Users->find('list', keyField: 'id', valueField: 'full_name_username');

        BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.beforeEditRender', $this, [
            'Role' => $role,
            'Users' => $users,
        ]);

        $this->set(compact('role', 'users'));
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
        $role = $this->Roles->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.beforeDelete', $this, [
            'Role' => $role,
        ]);
        // Role Admin, Manager and Public can not be deleted!
        if (($role->title === 'Admin') ||
            ($role->title === 'Manager') ||
            ($role->title === 'Public')
        ) {
            $this->Flash->set(
                __d('bechlem_connect_light', 'You are not allowed to delete this role.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );

            return $this->redirect(['action' => 'index']);
        }

        if ($this->Roles->delete($role)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.onDeleteSuccess', $this, [
                'Role' => $role,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The role has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Roles.onDeleteFailure', $this, [
                'Role' => $role,
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The role could not be deleted. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]);
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
        $roles = $this->Roles->find('all');
        $header = $this->Roles->tableColumns;

        $rolesArray = [];
        foreach($roles as $role) {
            $roleArray = [];
            $roleArray['id'] = $role->id;
            $roleArray['foreign_key'] = $role->foreign_key;
            $roleArray['title'] = $role->title;
            $roleArray['alias'] = $role->alias;
            $roleArray['created'] = empty($role->created)? NULL: $role->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $roleArray['modified'] = empty($role->modified)? NULL: $role->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $rolesArray[] = $roleArray;
        }
        $roles = $rolesArray;

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
        foreach ($roles as $dataEntity) {
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
        $roles = $this->Roles->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->Roles->tableColumns;
        $extract = [
            'id',
            'foreign_key',
            'title',
            'alias',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('roles'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'roles',
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
        $roles = $this->Roles->find('all');

        $rolesArray = [];
        foreach($roles as $role) {
            $roleArray = [];
            $roleArray['id'] = $role->id;
            $roleArray['foreign_key'] = $role->foreign_key;
            $roleArray['title'] = $role->title;
            $roleArray['alias'] = $role->alias;
            $roleArray['created'] = empty($role->created)? NULL: $role->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $roleArray['modified'] = empty($role->modified)? NULL: $role->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $rolesArray[] = $roleArray;
        }
        $roles = ['Roles' => ['Role' => $rolesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('roles'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'roles']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $roles = $this->Roles->find('all');

        $rolesArray = [];
        foreach($roles as $role) {
            $roleArray = [];
            $roleArray['id'] = $role->id;
            $roleArray['foreign_key'] = $role->foreign_key;
            $roleArray['title'] = $role->title;
            $roleArray['alias'] = $role->alias;
            $roleArray['created'] = empty($role->created)? NULL: $role->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $roleArray['modified'] = empty($role->modified)? NULL: $role->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $rolesArray[] = $roleArray;
        }
        $roles = ['Roles' => ['Role' => $rolesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('roles'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'roles']);
    }
}
