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
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Intervention\Image\ImageManager as ImageManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * UserProfiles Controller
 *
 * @property \BechlemConnectLight\Model\Table\UserProfilesTable $UserProfiles
 */
class UserProfilesController extends AppController
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
            'user_id',
            'foreign_key',
            'prefix',
            'salutation',
            'suffix',
            'first_name',
            'middle_name',
            'last_name',
            'gender',
            'birthday',
            'website',
            'telephone',
            'mobilephone',
            'fax',
            'company',
            'street',
            'street_addition',
            'postcode',
            'city',
            'country_id',
            'about_me',
            'tags',
            'timezone',
            'image',
            'view_counter',
            'status',
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
        $query = $this->UserProfiles
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->contain(['Users']);

        BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.beforeIndexRender', $this, [
            'Query' => $query,
        ]);

        $this->set('userProfiles', $this->paginate($query));
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return void
     */
    public function view(int $id = null)
    {
        $userProfile = $this->UserProfiles->get($id, contain: [
            'Users',
            'Countries',
        ]);

        $Users = TableRegistry::getTableLocator()->get('BechlemConnectLight.Users');
        $users = $Users->find('list', order: ['Users.name' => 'ASC'], keyField: 'id', valueField: 'name')
        ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.beforeViewRender', $this, [
            'UserProfile' => $userProfile,
            'Users' => $users,
        ]);

        $this->set(compact('userProfile', 'users'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function add()
    {
        $userProfile = $this->UserProfiles->newEmptyEntity();
        if ($this->getRequest()->is('post')) {

            $postData = $this->getRequest()->getData();

            if (
                !empty($postData['image_file']->getClientFileName()) &&
                !empty($postData['image_file']->getClientMediaType()) &&
                in_array($postData['image_file']->getClientMediaType(), [
                    'image/jpeg',
                    'image/jpg',
                ])
            ) {
                $profileRootPhotoUri = WWW_ROOT . 'img' . DS . h($postData['foreign_key']) . '_' . '.' . 'jpg';
                $profilePhotoUri = WWW_ROOT . 'img' . DS . h($postData['foreign_key']) . '.' . 'jpg';
                $profilePhotoContents = file_get_contents($postData['image_file']->getStream()->getMetadata('uri'));
                if ($profilePhotoContents) {
                    file_put_contents($profileRootPhotoUri, $profilePhotoContents);

                    $blankImageUri = ROOT . DS . 'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'img' . DS .  'blank_image_800' . '.' . 'jpg';
                    $size = getimagesize($profileRootPhotoUri);
                    $ratio = $size[0] / $size[1];
                    $dst_y = 0;
                    $dst_x = 0;
                    if ($ratio > 1) {
                        $width = 800;
                        $height = 800 / $ratio;
                        $dst_y = (800 - $height) / 2;
                    } else {
                        $width = 800 * $ratio;
                        $height = 800;
                        $dst_x = (800 - $width) / 2;
                    }
                    $src = imagecreatefromstring(file_get_contents($profileRootPhotoUri));
                    $dst = imagecreatetruecolor(intval($width), intval($height));
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, intval($width), intval($height), $size[0], $size[1]);
                    $blankImage = imagecreatefromjpeg($blankImageUri);
                    if (
                        imagecopymerge($blankImage, $dst, intval($dst_x), intval($dst_y), 0, 0, imagesx($dst), imagesy($dst), 100) &&
                        imagejpeg($blankImage, $profilePhotoUri)
                    ) {
                        unlink($profileRootPhotoUri);
                    }

                    ImageManager::configure(['driver' => 'imagick']);
                    $profilePhoto = ImageManager::make($profilePhotoUri);
                    $profilePhoto->resize(400, 400);
                    $profilePhoto->insert(ROOT . DS . 'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'img' . DS . 'watermark' . '.' . 'png');
                    $profilePhoto->save($profilePhotoUri);

                    $postData['image'] = '/bechlem_connect_light/img/avatars/' . h($postData['foreign_key']) . '.' . 'jpg';
                } else {
                    $postData['image'] = '/bechlem_connect_light/img/avatars/avatar.jpg';
                }
            }

            $user = $this->UserProfiles->patchEntity(
                $userProfile,
                Hash::merge($this->getRequest()->getData(), [
                    'foreign_key'       => h($postData['foreign_key']),
                    'first_name'        => h($postData['first_name']),
                    'middle_name'       => h($postData['middle_name']),
                    'last_name'         => h($postData['last_name']),
                    'website'           => h($postData['website']),
                    'telephone'         => h($postData['telephone']),
                    'mobilephone'       => h($postData['mobilephone']),
                    'fax'               => h($postData['fax']),
                    'company'           => h($postData['company']),
                    'street'            => h($postData['street']),
                    'street_addition'   => h($postData['street_addition']),
                    'postcode'          => h($postData['postcode']),
                    'city'              => h($postData['city']),
                    'about_me'          => h($postData['about_me']),
                    'tags'              => h($postData['tags']),
                    'image'             => h($postData['image']),
                    'view_counter'      => 0,
                ])
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.beforeAdd', $this, ['User' => $user]);
            if ($this->UserProfiles->save($user)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.onAddSuccess', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user profile has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.onAddFailure', $this, ['User' => $user]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user profile could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $users = $this->UserProfiles->Users->find('list', order: ['Users.name' => 'ASC'], keyField: 'id', valueField: 'name_username_email');

        BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.beforeAddRender', $this, [
            'UserProfile' => $userProfile,
            'Users' => $users,
        ]);

        $this->set(compact('userProfile', 'users'));
    }

    /**
     * Edit method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|void|null
     */
    public function edit(int $id = null)
    {
        $userProfile = $this->UserProfiles->get($id, contain: ['Users']);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {

            $postData = $this->getRequest()->getData();

            if (
                !empty($postData['image_file']->getClientFileName()) &&
                !empty($postData['image_file']->getClientMediaType()) &&
                in_array($postData['image_file']->getClientMediaType(), [
                    'image/jpeg',
                    'image/jpg',
                ])
            ) {
                $profileRootPhotoUri = WWW_ROOT . 'img' . DS . h($postData['foreign_key']) . '_' . '.' . 'jpg';
                $profilePhotoUri = WWW_ROOT . 'img' . DS . h($postData['foreign_key']) . '.' . 'jpg';
                $profilePhotoContents = file_get_contents($postData['image_file']->getStream()->getMetadata('uri'));
                if ($profilePhotoContents) {
                    file_put_contents($profileRootPhotoUri, $profilePhotoContents);

                    $blankImageUri = ROOT . DS . 'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'img' . DS . 'blank_image_800' . '.' . 'jpg';
                    $size = getimagesize($profileRootPhotoUri);
                    $ratio = $size[0] / $size[1];
                    $dst_y = 0;
                    $dst_x = 0;
                    if ($ratio > 1) {
                        $width = 800;
                        $height = 800 / $ratio;
                        $dst_y = (800 - $height) / 2;
                    } else {
                        $width = 800 * $ratio;
                        $height = 800;
                        $dst_x = (800 - $width) / 2;
                    }
                    $src = imagecreatefromstring(file_get_contents($profileRootPhotoUri));
                    $dst = imagecreatetruecolor(intval($width), intval($height));
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, intval($width), intval($height), $size[0], $size[1]);
                    $blankImage = imagecreatefromjpeg($blankImageUri);
                    if (
                        imagecopymerge($blankImage, $dst, intval($dst_x), intval($dst_y), 0, 0, imagesx($dst), imagesy($dst), 100) &&
                        imagejpeg($blankImage, $profilePhotoUri)
                    ) {
                        unlink($profileRootPhotoUri);
                    }

                    ImageManager::configure(['driver' => 'imagick']);
                    $profilePhoto = ImageManager::make($profilePhotoUri);
                    $profilePhoto->resize(400, 400);
                    $profilePhoto->insert(ROOT . DS . 'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'img' . DS . 'watermark' . '.' . 'png');
                    $profilePhoto->save($profilePhotoUri);

                    $postData['image'] = '/bechlem_connect_light/img/avatars/' . h($postData['foreign_key']) . '.' . 'jpg';
                } else {
                    $postData['image'] = '/bechlem_connect_light/img/avatars/avatar.jpg';
                }
            }

            $userProfile = $this->UserProfiles->patchEntity(
                $userProfile,
                Hash::merge(
                    $this->getRequest()->getData(),
                    [
                        'foreign_key'       => h($postData['foreign_key']),
                        'first_name'        => h($postData['first_name']),
                        'middle_name'       => h($postData['middle_name']),
                        'last_name'         => h($postData['last_name']),
                        'website'           => h($postData['website']),
                        'telephone'         => h($postData['telephone']),
                        'mobilephone'       => h($postData['mobilephone']),
                        'fax'               => h($postData['fax']),
                        'company'           => h($postData['company']),
                        'street'            => h($postData['street']),
                        'street_addition'   => h($postData['street_addition']),
                        'postcode'          => h($postData['postcode']),
                        'city'              => h($postData['city']),
                        'about_me'          => h($postData['about_me']),
                        'tags'              => h($postData['tags']),
                        'image'             => h($postData['image']),
                        'status'            => h($postData['status']),
                    ]
                )
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.beforeEdit', $this, ['UserProfile' => $userProfile]);
            if ($this->UserProfiles->save($userProfile)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.onEditSuccess', $this, ['UserProfile' => $userProfile]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user profile has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.onEditFailure', $this, ['UserProfile' => $userProfile]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The user profile could not be saved. Please, try again.'),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $users = $this->UserProfiles->Users->find('list', order: ['Users.name' => 'ASC'], keyField: 'id', valueField: 'name_username_email');

        BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.beforeEditRender', $this, [
            'UserProfile' => $userProfile,
            'Users' => $users,
        ]);

        $this->set(compact('userProfile', 'users'));
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
        $userProfile = $this->UserProfiles->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.beforeDelete', $this, ['UserProfile' => $userProfile]);
        if ($this->UserProfiles->delete($userProfile)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.onDeleteSuccess', $this, ['UserProfile' => $userProfile]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The user profile has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.UserProfiles.onDeleteFailure', $this, ['UserProfile' => $userProfile]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The user profile could not be deleted. Please, try again.'),
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
        $userProfiles = $this->UserProfiles->find('all');
        $header = $this->UserProfiles->tableColumns;

        $userProfilesArray = [];
        foreach($userProfiles as $userProfile) {
            $userProfileArray = [];
            $userProfileArray['id'] = $userProfile->id;
            $userProfileArray['user_id'] = $userProfile->user_id;
            $userProfileArray['foreign_key'] = $userProfile->foreign_key;
            $userProfileArray['prefix'] = $userProfile->prefix;
            $userProfileArray['salutation'] = $userProfile->salutation;
            $userProfileArray['suffix'] = $userProfile->suffix;
            $userProfileArray['first_name'] = $userProfile->first_name;
            $userProfileArray['middle_name'] = $userProfile->middle_name;
            $userProfileArray['last_name'] = $userProfile->last_name;
            $userProfileArray['gender'] = $userProfile->gender;
            $userProfileArray['birthday'] = empty($userProfile->birthday)? NULL: $userProfile->birthday->i18nFormat('yyyy-MM-dd');
            $userProfileArray['website'] = $userProfile->website;
            $userProfileArray['telephone'] = $userProfile->telephone;
            $userProfileArray['mobilephone'] = $userProfile->mobilephone;
            $userProfileArray['fax'] = $userProfile->fax;
            $userProfileArray['company'] = $userProfile->company;
            $userProfileArray['street'] = $userProfile->street;
            $userProfileArray['street_addition'] = $userProfile->street_addition;
            $userProfileArray['postcode'] = $userProfile->postcode;
            $userProfileArray['city'] = $userProfile->city;
            $userProfileArray['country_id'] = $userProfile->country_id;
            $userProfileArray['about_me'] = $userProfile->about_me;
            $userProfileArray['tags'] = $userProfile->tags;
            $userProfileArray['timezone'] = $userProfile->timezone;
            $userProfileArray['image'] = $userProfile->image;
            $userProfileArray['view_counter'] = $userProfile->view_counter;
            $userProfileArray['status'] = ($userProfile->status == 1)? 1: 0;
            $userProfileArray['created'] = empty($userProfile->created)? NULL: $userProfile->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userProfileArray['modified'] = empty($userProfile->modified)? NULL: $userProfile->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $userProfilesArray[] = $userProfileArray;
        }
        $userProfiles = $userProfilesArray;

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
                case 27: $col = 'AA'; break;
                case 28: $col = 'AB'; break;
                case 29: $col = 'AC'; break;
                case 30: $col = 'AD'; break;
                case 31: $col = 'AE'; break;
                case 32: $col = 'AF'; break;
                case 33: $col = 'AG'; break;
                case 34: $col = 'AH'; break;
                case 35: $col = 'AI'; break;
                case 36: $col = 'AJ'; break;
                case 37: $col = 'AK'; break;
                case 38: $col = 'AL'; break;
                case 39: $col = 'AM'; break;
                case 40: $col = 'AN'; break;
                case 41: $col = 'AO'; break;
                case 42: $col = 'AP'; break;
                case 43: $col = 'AQ'; break;
                case 44: $col = 'AR'; break;
                case 45: $col = 'AS'; break;
                case 46: $col = 'AT'; break;
                case 47: $col = 'AU'; break;
                case 48: $col = 'AV'; break;
                case 49: $col = 'AW'; break;
                case 50: $col = 'AX'; break;
                case 51: $col = 'AY'; break;
                case 52: $col = 'AZ'; break;
            }

            $objSpreadsheet->getActiveSheet()->setCellValue($col . $rowCount, $headerAlias);
            $colCount++;
        }

        $rowCount = 1;
        foreach ($userProfiles as $dataEntity) {
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
                    case 27: $col = 'AA'; break;
                    case 28: $col = 'AB'; break;
                    case 29: $col = 'AC'; break;
                    case 30: $col = 'AD'; break;
                    case 31: $col = 'AE'; break;
                    case 32: $col = 'AF'; break;
                    case 33: $col = 'AG'; break;
                    case 34: $col = 'AH'; break;
                    case 35: $col = 'AI'; break;
                    case 36: $col = 'AJ'; break;
                    case 37: $col = 'AK'; break;
                    case 38: $col = 'AL'; break;
                    case 39: $col = 'AM'; break;
                    case 40: $col = 'AN'; break;
                    case 41: $col = 'AO'; break;
                    case 42: $col = 'AP'; break;
                    case 43: $col = 'AQ'; break;
                    case 44: $col = 'AR'; break;
                    case 45: $col = 'AS'; break;
                    case 46: $col = 'AT'; break;
                    case 47: $col = 'AU'; break;
                    case 48: $col = 'AV'; break;
                    case 49: $col = 'AW'; break;
                    case 50: $col = 'AX'; break;
                    case 51: $col = 'AY'; break;
                    case 52: $col = 'AZ'; break;
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
        $userProfiles = $this->UserProfiles->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->UserProfiles->tableColumns;
        $extract = [
            'id',
            'user_id',
            'foreign_key',
            'prefix',
            'salutation',
            'suffix',
            'first_name',
            'middle_name',
            'last_name',
            'gender',
            function ($row) {
                return empty($row['birthday'])? NULL: $row['birthday']->i18nFormat('yyyy-MM-dd');
            },
            'website',
            'telephone',
            'mobilephone',
            'fax',
            'company',
            'street',
            'street_addition',
            'postcode',
            'city',
            'country_id',
            'about_me',
            'tags',
            'timezone',
            'image',
            'view_counter',
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
        $this->set(compact('userProfiles'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'userProfiles',
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
        $userProfiles = $this->UserProfiles->find('all');

        $userProfilesArray = [];
        foreach($userProfiles as $userProfile) {
            $userProfileArray = [];
            $userProfileArray['id'] = $userProfile->id;
            $userProfileArray['user_id'] = $userProfile->user_id;
            $userProfileArray['foreign_key'] = $userProfile->foreign_key;
            $userProfileArray['prefix'] = $userProfile->prefix;
            $userProfileArray['salutation'] = $userProfile->salutation;
            $userProfileArray['suffix'] = $userProfile->suffix;
            $userProfileArray['first_name'] = $userProfile->first_name;
            $userProfileArray['middle_name'] = $userProfile->middle_name;
            $userProfileArray['last_name'] = $userProfile->last_name;
            $userProfileArray['gender'] = $userProfile->gender;
            $userProfileArray['birthday'] = empty($userProfile->birthday)? NULL: $userProfile->birthday->i18nFormat('yyyy-MM-dd');
            $userProfileArray['website'] = $userProfile->website;
            $userProfileArray['telephone'] = $userProfile->telephone;
            $userProfileArray['mobilephone'] = $userProfile->mobilephone;
            $userProfileArray['fax'] = $userProfile->fax;
            $userProfileArray['company'] = $userProfile->company;
            $userProfileArray['street'] = $userProfile->street;
            $userProfileArray['street_addition'] = $userProfile->street_addition;
            $userProfileArray['postcode'] = $userProfile->postcode;
            $userProfileArray['city'] = $userProfile->city;
            $userProfileArray['country_id'] = $userProfile->country_id;
            $userProfileArray['about_me'] = $userProfile->about_me;
            $userProfileArray['tags'] = $userProfile->tags;
            $userProfileArray['timezone'] = $userProfile->timezone;
            $userProfileArray['image'] = $userProfile->image;
            $userProfileArray['view_counter'] = $userProfile->view_counter;
            $userProfileArray['status'] = ($userProfile->status == 1)? 1: 0;
            $userProfileArray['created'] = empty($userProfile->created)? NULL: $userProfile->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userProfileArray['modified'] = empty($userProfile->modified)? NULL: $userProfile->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $userProfilesArray[] = $userProfileArray;
        }
        $userProfiles = ['UserProfiles' => ['UserProfile' => $userProfilesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('userProfiles'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'userProfiles']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $userProfiles = $this->UserProfiles->find('all');

        $userProfilesArray = [];
        foreach($userProfiles as $userProfile) {
            $userProfileArray = [];
            $userProfileArray['id'] = $userProfile->id;
            $userProfileArray['user_id'] = $userProfile->user_id;
            $userProfileArray['foreign_key'] = $userProfile->foreign_key;
            $userProfileArray['prefix'] = $userProfile->prefix;
            $userProfileArray['salutation'] = $userProfile->salutation;
            $userProfileArray['suffix'] = $userProfile->suffix;
            $userProfileArray['first_name'] = $userProfile->first_name;
            $userProfileArray['middle_name'] = $userProfile->middle_name;
            $userProfileArray['last_name'] = $userProfile->last_name;
            $userProfileArray['gender'] = $userProfile->gender;
            $userProfileArray['birthday'] = empty($userProfile->birthday)? NULL: $userProfile->birthday->i18nFormat('yyyy-MM-dd');
            $userProfileArray['website'] = $userProfile->website;
            $userProfileArray['telephone'] = $userProfile->telephone;
            $userProfileArray['mobilephone'] = $userProfile->mobilephone;
            $userProfileArray['fax'] = $userProfile->fax;
            $userProfileArray['company'] = $userProfile->company;
            $userProfileArray['street'] = $userProfile->street;
            $userProfileArray['street_addition'] = $userProfile->street_addition;
            $userProfileArray['postcode'] = $userProfile->postcode;
            $userProfileArray['city'] = $userProfile->city;
            $userProfileArray['country_id'] = $userProfile->country_id;
            $userProfileArray['about_me'] = $userProfile->about_me;
            $userProfileArray['tags'] = $userProfile->tags;
            $userProfileArray['timezone'] = $userProfile->timezone;
            $userProfileArray['image'] = $userProfile->image;
            $userProfileArray['view_counter'] = $userProfile->view_counter;
            $userProfileArray['status'] = ($userProfile->status == 1)? 1: 0;
            $userProfileArray['created'] = empty($userProfile->created)? NULL: $userProfile->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userProfileArray['modified'] = empty($userProfile->modified)? NULL: $userProfile->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $userProfilesArray[] = $userProfileArray;
        }
        $userProfiles = ['UserProfiles' => ['UserProfile' => $userProfilesArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('userProfiles'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'userProfiles']);
    }
}
