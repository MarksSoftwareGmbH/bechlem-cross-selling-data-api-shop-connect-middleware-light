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
 * BechlemPrinterToSupplies Model
 *
 * @method \BechlemConnectLight\Model\Entity\BechlemPrinterToSupply get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemPrinterToSupply newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemPrinterToSupply[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemPrinterToSupply|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemPrinterToSupply patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemPrinterToSupply[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemPrinterToSupply findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BechlemPrinterToSuppliesTable extends Table
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

        $this->setTable('bechlem_printer_to_supplies');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

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
                    'id_item_printer',
                    'id_item_supply',
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
        'id_item_printer',
        'id_item_supply',
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
            ->requirePresence('id_item_printer', 'create')
            ->notBlank('id_item_printer');

        $validator
            ->requirePresence('id_item_supply', 'create')
            ->notBlank('id_item_supply');

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
     * Update printer to supplies method.
     * 
     * @param object|null $controller
     * 
     * @return bool
     */
    public function updatePrinterToSupplies(object $controller = null)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '900');

        // BechlemConnectRequests
        $BechlemConnectRequests = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemConnectRequests');
        try {
            $bechlemPrinterToSuppliesRequest = $BechlemConnectRequests
                ->find()
                ->contain(['BechlemConnectConfigs'])
                ->where([
                    'BechlemConnectRequests.slug'   => 'printer2supply',
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

        try {
            if (!empty($bechlemPrinterToSuppliesRequest->id)) {
                $bechlemPrinterToSuppliesResponse = $BechlemConnectRequests->runRequest(
                    $controller,
                    $bechlemPrinterToSuppliesRequest
                );
            }
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        if (empty($bechlemPrinterToSuppliesResponse)) {
            return false;
        }

        $bechlemPrinterToSupplies = [];
        foreach ($bechlemPrinterToSuppliesResponse as $data) {
            $bechlemPrinterToSupplies[$data['iditem_printer'] . $data['iditem_supply']] = [
                'printer' => $data['iditem_printer'],
                'supply' => $data['iditem_supply'],
            ];
        }

        $connection = ConnectionManager::get('default');
        try {
            $connection->execute('TRUNCATE TABLE bechlem_printer_to_supplies');
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        try {
            foreach ($bechlemPrinterToSupplies as $supply) {
                $connection->insert('bechlem_printer_to_supplies', [
                    'uuid_id'           => Text::uuid(),
                    'id_item_printer'   => trim($supply['printer']),
                    'id_item_supply'    => trim($supply['supply']),
                    'created'           => new DateTime('now'),
                    'modified'          => new DateTime('now'),
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
