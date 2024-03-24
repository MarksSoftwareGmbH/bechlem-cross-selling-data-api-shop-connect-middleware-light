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
 * BechlemProductAccessories Model
 *
 * @method \BechlemConnectLight\Model\Entity\BechlemProductAccessory get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProductAccessory newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProductAccessory[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProductAccessory|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProductAccessory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProductAccessory[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProductAccessory findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BechlemProductAccessoriesTable extends Table
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

        $this->setTable('bechlem_product_accessories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

        $this->belongsTo('BechlemProducts', [
            'foreignKey'    => 'referenced_product_id',
            'bindingKey'    => 'bechlem_id',
            'className'     => 'BechlemConnectLight.BechlemProducts',
        ]);

        $this->belongsTo('BechlemPrinters', [
            'foreignKey'    => 'referenced_product_id',
            'bindingKey'    => 'id_item',
            'className'     => 'BechlemConnectLight.BechlemPrinters',
        ]);

        $this->belongsTo('BechlemSupplies', [
            'foreignKey'    => 'referenced_product_id',
            'bindingKey'    => 'id_item',
            'className'     => 'BechlemConnectLight.BechlemSupplies',
        ]);

        // Setup search filter using search manager
        $this->searchManager()
            ->add('search', 'Search.Like', [
                'before'        => true,
                'after'         => true,
                'fieldMode'     => 'OR',
                'comparison'    => 'LIKE',
                'wildcardAny'   => '*',
                'wildcardOne'   => '?',
                'fields' => [
                    'bechlem_product_id',
                    'type',
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
        'bechlem_product_id',
        'referenced_product_id',
        'type',
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
            ->requirePresence('bechlem_product_id', 'create')
            ->notBlank('bechlem_product_id');

        $validator
            ->requirePresence('referenced_product_id', 'create')
            ->notBlank('referenced_product_id');

        $validator
            ->requirePresence('type', 'create')
            ->notBlank('type');

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
     * Update products method.
     *
     * @return bool
     */
    public function updateProductAccessories()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '900');

        // BechlemPrinterToSupplies
        $BechlemPrinterToSupplies = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemPrinterToSupplies');
        try {
            $bechlemPrinterToSupplies = $BechlemPrinterToSupplies
                ->find('all')
                ->toArray();
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }
        if (empty($bechlemPrinterToSupplies)) {
            return false;
        }

        $bechlemProductAccessories = [];
        foreach ($bechlemPrinterToSupplies as $data) {
            $bechlemProductAccessories[$data['id_item_printer'] . $data['id_item_supply']] = [
                'supply'        => $data['id_item_printer'],
                'referenced'    => $data['id_item_supply'],
                'type'          => 'Printer',
            ];
        }
        foreach ($bechlemPrinterToSupplies as $data) {
            $bechlemProductAccessories[$data['id_item_supply'] . $data['id_item_printer']] = [
                'supply'        => $data['id_item_supply'],
                'referenced'    => $data['id_item_printer'],
                'type'          => 'Printer',
            ];
        }

        // BechlemSupplyToSupplies
        $BechlemSupplyToSupplies = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemSupplyToSupplies');
        try {
            $bechlemSupplyToSupplies = $BechlemSupplyToSupplies
                ->find('all')
                ->toArray();
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }
        if (empty($bechlemSupplyToSupplies)) {
            return false;
        }
        foreach ($bechlemSupplyToSupplies as $data) {
            $bechlemProductAccessories[$data['id_item_supply'] . $data['id_item_supply_2']] = [
                'supply'        => $data['id_item_supply'],
                'referenced'    => $data['id_item_supply_2'],
                'type'          => 'Supply',
            ];
        }
        foreach ($bechlemSupplyToSupplies as $data) {
            $bechlemProductAccessories[$data['id_item_supply_2'] . $data['id_item_supply']] = [
                'supply'        => $data['id_item_supply_2'],
                'referenced'    => $data['id_item_supply'],
                'type'          => 'Supply',
            ];
        }

        $connection = ConnectionManager::get('default');
        try {
            $connection->execute('TRUNCATE TABLE bechlem_product_accessories');
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        try {
            foreach ($bechlemProductAccessories as $accessory) {
                $connection->insert('bechlem_product_accessories', [
                    'uuid_id'               => Text::uuid(),
                    'bechlem_product_id'    => trim($accessory['supply']),
                    'referenced_product_id' => trim($accessory['referenced']),
                    'type'                  => trim($accessory['type']),
                    'created'               => new DateTime('now'),
                    'modified'              => new DateTime('now'),
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
