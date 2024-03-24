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
namespace BechlemConnectLight\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use DateTime;
use Exception;
use Psr\Log\LogLevel;

/**
 * BechlemResellerItems Model
 *
 * @method \BechlemConnectLight\Model\Entity\BechlemResellerItem get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemResellerItem newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemResellerItem[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemResellerItem|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemResellerItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemResellerItem[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemResellerItem findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BechlemResellerItemsTable extends Table
{

    /**
     * Initialize a table instance. Called after the constructor.
     *
     * You can use this method to define associations, attach behaviors
     * define validation and do any other initialization logic you need.
     *
     * ```
     *  public function initialize(array $config)
     *  {
     *      $this->belongsTo('Users');
     *      $this->belongsToMany('Tagging.Tags');
     *      $this->setPrimaryKey('something_else');
     *  }
     * ```
     *
     * @param array $config Configuration options passed to the constructor
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('bechlem_reseller_items');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

        $this->hasOne('BechlemPrinters', [
            'foreignKey' => 'id_item',
            'bindingKey' => 'id_item',
            'className' => 'BechlemConnectLight.BechlemPrinters',
        ]);

        $this->hasOne('BechlemSupplies', [
            'foreignKey' => 'id_item',
            'bindingKey' => 'id_item',
            'className' => 'BechlemConnectLight.BechlemSupplies',
        ]);

        // Setup search filter using search manager
        $this->searchManager()
            ->add('search', 'Search.Like', [
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'fields' => [
                    'id_art_nr',
                    'id_reseller',
                    'id_item',
                    'ean',
                    'oem_nr',
                    'description',
                    'language',
                ],
            ]);
    }

    /**
     * Default table columns.
     *
     * @var array
     */
    public $tableColumns = [
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
    ];

    /**
     * Returns the default validator object. Subclasses can override this function
     * to add a default validation set to the validator object.
     *
     * @param \Cake\Validation\Validator $validator The validator that can be modified to
     * add some rules to it.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('id_art_nr', 'create')
            ->notBlank('id_art_nr');

        $validator
            ->requirePresence('id_reseller', 'create')
            ->notBlank('id_reseller');

        $validator
            ->requirePresence('id_item', 'create')
            ->notBlank('id_item');

        $validator
            ->requirePresence('ean', 'create')
            ->notBlank('ean');

        $validator
            ->requirePresence('oem_nr', 'create')
            ->notBlank('oem_nr');

        $validator
            ->requirePresence('description', 'create')
            ->notBlank('description');

        $validator
            ->requirePresence('ve', 'create')
            ->notBlank('ve');

        $validator
            ->requirePresence('language', 'create')
            ->notBlank('language');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

        $validator
            ->integer('deleted_by')
            ->allowEmptyString('deleted_by');

        return $validator;
    }

    /**
     * Returns a RulesChecker object after modifying the one that was supplied.
     *
     * Subclasses should override this method in order to initialize the rules to be applied to
     * entities saved by this instance.
     *
     * @param \Cake\Datasource\RulesChecker $rules The rules object to be modified.
     * @return \Cake\Datasource\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        return $rules;
    }

    /**
     * Update reseller items method.
     *
     * @return bool
     */
    public function updateResellerItems()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '900');

        // BechlemConnectRequests
        $BechlemConnectRequests = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemConnectRequests');
        try {
            $bechlemResellerItemsRequest = $BechlemConnectRequests
                ->find()
                ->contain(['BechlemConnectConfigs'])
                ->where([
                    'BechlemConnectRequests.slug'   => 'reselleritem',
                    'BechlemConnectRequests.method' => 'GET',
                    'BechlemConnectRequests.status' => 1,
                ])
                ->matching('BechlemConnectConfigs', function ($q) {
                    return $q->where([
                        'BechlemConnectConfigs.alias'   => 'datawriter',
                        'BechlemConnectConfigs.status'  => 1,
                    ]);
                })
                ->first();
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        $bechlemResellerItemsRequestLanguage = 'en';
        if (!empty($bechlemResellerItemsRequest->language)) {
            $bechlemResellerItemsRequestLanguage = $bechlemResellerItemsRequest->language;
        }

        try {
            if (!empty($bechlemResellerItemsRequest->id)) {
                $bechlemResellerItemsResponse = $BechlemConnectRequests->runRequest(
                    null,
                    $bechlemResellerItemsRequest
                );
            }
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        if (empty($bechlemResellerItemsResponse)) {
            return false;
        }

        $connection = ConnectionManager::get('default');
        try {
            $connection->execute('TRUNCATE TABLE bechlem_reseller_items');
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        try {
            foreach ($bechlemResellerItemsResponse as $bechlemResellerItem) {

                if (
                    empty($bechlemResellerItem['idartnr']) &&
                    empty($bechlemResellerItem['idreseller']) &&
                    empty($bechlemResellerItem['iditem'])
                ) {
                    continue;
                }

                $connection->insert('bechlem_reseller_items', [
                    'uuid_id'       => Text::uuid(),
                    'id_art_nr'     => trim($bechlemResellerItem['idartnr']),
                    'id_reseller'   => trim($bechlemResellerItem['idreseller']),
                    'id_item'       => trim($bechlemResellerItem['iditem']),
                    'ean'           => trim($bechlemResellerItem['ean']),
                    'oem_nr'        => trim($bechlemResellerItem['oemnr']),
                    'description'   => trim($bechlemResellerItem['description']),
                    've'            => trim($bechlemResellerItem['ve']),
                    'language'      => trim($bechlemResellerItemsRequestLanguage),
                    'created'       => new DateTime('now'),
                    'modified'      => new DateTime('now'),
                ], [
                    'created'   => 'datetime',
                    'modified'  => 'datetime',
                ]);
            }
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        return true;
    }
}
