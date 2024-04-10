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
 * BechlemSupplies Model
 *
 * @method \BechlemConnectLight\Model\Entity\BechlemSupply get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemSupply newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemSupply[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemSupply|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemSupply patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemSupply[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemSupply findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BechlemSuppliesTable extends Table
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

        $this->setTable('bechlem_supplies');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

        $this->belongsTo('BechlemResellerItems', [
            'foreignKey'    => 'id_item',
            'bindingKey'    => 'id_item',
            'className'     => 'BechlemConnectLight.BechlemResellerItems',
        ]);

        $this->hasMany('BechlemIdentifiers', [
            'foreignKey'    => 'id_item',
            'bindingKey'    => 'id_item',
            'className'     => 'BechlemConnectLight.BechlemIdentifiers',
            'sort'          => ['BechlemIdentifiers.id_position' => 'ASC']
        ]);

        $this->hasMany('BechlemSupplyToSupplies', [
            'foreignKey'    => 'id_item_supply',
            'bindingKey'    => 'id_item',
            'className'     => 'BechlemConnectLight.BechlemSupplyToSupplies',
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
                    'id_item',
                    'id_brand',
                    'brand',
                    'art_nr',
                    'part_nr',
                    'name',
                    'id_category',
                    'category',
                    'ean',
                    'picture',
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
            ->allowEmptyString('id_item');

        $validator
            ->allowEmptyString('id_brand');

        $validator
            ->allowEmptyString('brand');

        $validator
            ->allowEmptyString('art_nr');

        $validator
            ->allowEmptyString('part_nr');

        $validator
            ->allowEmptyString('name');

        $validator
            ->allowEmptyString('id_category');

        $validator
            ->allowEmptyString('category');

        $validator
            ->allowEmptyString('color');

        $validator
            ->allowEmptyString('is_compatible');

        $validator
            ->allowEmptyString('ve');

        $validator
            ->allowEmptyString('yield');

        $validator
            ->allowEmptyString('coverage');

        $validator
            ->allowEmptyString('measures');

        $validator
            ->allowEmptyString('content');

        $validator
            ->allowEmptyString('content_ml');

        $validator
            ->allowEmptyString('content_gram');

        $validator
            ->allowEmptyString('content_char');

        $validator
            ->allowEmptyString('german_group_no');

        $validator
            ->allowEmptyString('supply_series');

        $validator
            ->allowEmptyString('ean');

        $validator
            ->allowEmptyString('picture');
        
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
     * Update supplies method.
     * 
     * @param object|null $controller
     * 
     * @return bool
     */
    public function updateSupplies(object $controller = null)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '900');

        // BechlemConnectRequests
        $BechlemConnectRequests = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemConnectRequests');
        try {
            $bechlemSuppliesRequest = $BechlemConnectRequests
                ->find()
                ->contain(['BechlemConnectConfigs'])
                ->where([
                    'BechlemConnectRequests.slug'   => 'supply',
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

        $bechlemSuppliesRequestLanguage = 'en';
        if (!empty($bechlemSuppliesRequest->language)) {
            $bechlemSuppliesRequestLanguage = $bechlemSuppliesRequest->language;
        }

        try {
            if (!empty($bechlemSuppliesRequest->id)) {
                $bechlemSuppliesResponse = $BechlemConnectRequests->runRequest(
                    $controller,
                    $bechlemSuppliesRequest
                );
            }
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        if (empty($bechlemSuppliesResponse)) {
            return false;
        }

        $connection = ConnectionManager::get('default');
        try {
            $connection->execute('TRUNCATE TABLE bechlem_supplies');
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        try {
            foreach ($bechlemSuppliesResponse as $bechlemSupply) {

                if (
                    empty($bechlemSupply['iditem']) &&
                    empty($bechlemSupply['artnr'])
                ) {
                    continue;
                }

                $connection->insert('bechlem_supplies', [
                    'uuid_id'           => Text::uuid(),
                    'id_item'           => trim($bechlemSupply['iditem']),
                    'id_brand'          => trim($bechlemSupply['idbrand']),
                    'brand'             => trim($bechlemSupply['brand']),
                    'art_nr'            => trim($bechlemSupply['artnr']),
                    'part_nr'           => trim($bechlemSupply['partnr']),
                    'name'              => trim($bechlemSupply['text']),
                    'id_category'       => trim($bechlemSupply['idcategory']),
                    'category'          => trim($bechlemSupply['category']),
                    'color'             => trim($bechlemSupply['color']),
                    'is_compatible'     => trim($bechlemSupply['iscompatible']),
                    've'                => trim($bechlemSupply['nzve']),
                    'yield'             => trim($bechlemSupply['nzyield']),
                    'coverage'          => trim($bechlemSupply['coverage']),
                    'measures'          => trim($bechlemSupply['measures']),
                    'content'           => trim($bechlemSupply['content']),
                    'content_ml'        => trim($bechlemSupply['content_ml']),
                    'content_gram'      => trim($bechlemSupply['content_gramm']),
                    'content_char'      => trim($bechlemSupply['content_char']),
                    'german_group_no'   => trim($bechlemSupply['german_group_no']),
                    'supply_series'     => trim($bechlemSupply['supplyseries']),
                    'ean'               => trim($bechlemSupply['ean']),
                    'picture'           => trim($bechlemSupply['nzpicture']),
                    'language'          => trim($bechlemSuppliesRequestLanguage),
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
