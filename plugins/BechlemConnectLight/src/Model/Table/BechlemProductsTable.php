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
 * BechlemProducts Model
 *
 * @method \BechlemConnectLight\Model\Entity\BechlemProduct get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProduct newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProduct[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProduct|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProduct patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProduct[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemProduct findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BechlemProductsTable extends Table
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

        $this->setTable('bechlem_products');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

        $this->hasMany('BechlemProductAccessories', [
            'foreignKey' => 'bechlem_product_id',
            'bindingKey' => 'bechlem_id',
            'className' => 'BechlemConnectLight.BechlemProductAccessories'
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
                    'bechlem_id',
                    'ean',
                    'manufacturer_sku',
                    'your_sku',
                    'manufacturer_name',
                    'product_name_with_manufacturer',
                    'short_description',
                    'product_type_name',
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
        'bechlem_id',
        'ean',
        'manufacturer_sku',
        'your_sku',
        'manufacturer_id',
        'manufacturer_name',
        'product_name_with_manufacturer',
        'short_description',
        'product_type_id',
        'product_type_name',
        'image',
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
            ->requirePresence('bechlem_id', 'create')
            ->notBlank('bechlem_id');

        $validator
            ->requirePresence('your_sku', 'create')
            ->notBlank('your_sku');

        $validator
            ->requirePresence('manufacturer_sku', 'create')
            ->notBlank('manufacturer_sku');

        $validator
            ->allowEmptyString('ean');

        $validator
            ->allowEmptyString('manufacturer_id');

        $validator
            ->allowEmptyString('manufacturer_name');

        $validator
            ->allowEmptyString('product_name_with_manufacturer');

        $validator
            ->allowEmptyString('short_description');

        $validator
            ->allowEmptyString('product_type_id');

        $validator
            ->allowEmptyString('product_type_name');

        $validator
            ->allowEmptyString('image');

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
    public function updateProducts()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '900');

        // BechlemResellerItems
        $BechlemResellerItems = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemResellerItems');
        try {
            $bechlemResellerItems = $BechlemResellerItems
                ->find('all')
                ->contain([
                    'BechlemSupplies.BechlemIdentifiers',
                    'BechlemSupplies.BechlemSupplyToSupplies',
                    'BechlemPrinters.BechlemIdentifiers',
                    'BechlemPrinters.BechlemPrinterToSupplies',
                ])
                ->toArray();
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        if (empty($bechlemResellerItems)) {
            return false;
        }

        $connection = ConnectionManager::get('default');
        try {
            $connection->execute('TRUNCATE TABLE bechlem_products');
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);

            return false;
        }

        try {
            foreach ($bechlemResellerItems as $bechlemResellerItem) {

                if (empty($bechlemResellerItem->id_art_nr)) { continue; }

                $ean = '';
                $manufacturerSku = '';
                $manufacturerId = '';
                $manufacturerName = '';
                $productNameWithManufacturer = '';
                $shortDescription = '';
                $productTypeId = '';
                $productTypeName = '';
                $image = '';

                if (!empty($bechlemResellerItem->bechlem_supply)) {

                    $manufacturerId = $bechlemResellerItem->bechlem_supply->id_brand;
                    $manufacturerName = $bechlemResellerItem->bechlem_supply->brand;

                    $partnr = '';
                    
                    if (
                        is_array($bechlemResellerItem->bechlem_supply->bechlem_identifiers) &&
                        !empty($bechlemResellerItem->bechlem_supply->bechlem_identifiers)
                    ) {
                        foreach ($bechlemResellerItem->bechlem_supply->bechlem_identifiers as $bechlemIdentifier) {
                            if (($bechlemIdentifier->id_type === 'PARTNR') && !empty($bechlemIdentifier->sync_id) && empty($partnr)) {
                                $partnr .= $bechlemIdentifier->sync_id;
                            } elseif (($bechlemIdentifier->id_type === 'PARTNR') && !empty($bechlemIdentifier->sync_id) && !empty($partnr)) {
                                $partnr .= ',' . $bechlemIdentifier->sync_id;
                            }

                            if (($bechlemIdentifier->id_type === 'ARTNR') && !empty($bechlemIdentifier->sync_id) && empty($manufacturerSku)) {
                                $manufacturerSku .= $bechlemIdentifier->sync_id;
                            } elseif (($bechlemIdentifier->id_type === 'ARTNR') && !empty($bechlemIdentifier->sync_id) && !empty($manufacturerSku)) {
                                $manufacturerSku .= ',' . $bechlemIdentifier->sync_id;
                            }

                            if (($bechlemIdentifier->id_type === 'EAN') && !empty($bechlemIdentifier->sync_id) && empty($ean)) {
                                $ean .= $bechlemIdentifier->sync_id;
                            } elseif (($bechlemIdentifier->id_type === 'EAN') && !empty($bechlemIdentifier->sync_id) && !empty($ean)) {
                                $ean .= ',' . $bechlemIdentifier->sync_id;
                            }
                        }
                    }

                    $productNameWithManufacturer = $bechlemResellerItem->bechlem_supply->brand
                        . ' '
                        . $bechlemResellerItem->bechlem_supply->part_nr
                        . ' '
                        . $bechlemResellerItem->bechlem_supply->name;
                    if (!empty($bechlemResellerItem->bechlem_supply->art_nr)) {
                        $productNameWithManufacturer .= ' - '
                            . $bechlemResellerItem->bechlem_supply->art_nr;
                    }

                    if (!empty($partnr)) {
                        $productNameWithManufacturer = $bechlemResellerItem->bechlem_supply->brand
                            . ' '
                            . $partnr
                            . ' '
                            . $bechlemResellerItem->bechlem_supply->name;
                        if (!empty($manufacturerSku)) {
                            $productNameWithManufacturer .= ' - '
                                . $manufacturerSku;
                        }
                    }

                    if (!empty($bechlemResellerItem->bechlem_supply->content)) {
                        $shortDescription .= $bechlemResellerItem->bechlem_supply->content;
                    }

                    $productTypeId = $bechlemResellerItem->bechlem_supply->id_category;
                    $productTypeName = $bechlemResellerItem->bechlem_supply->category;

                    $image = $bechlemResellerItem->bechlem_supply->picture;

                } elseif (!empty($bechlemResellerItem->bechlem_printer)) {

                    $manufacturerId = $bechlemResellerItem->bechlem_printer->id_brand;
                    $manufacturerName = $bechlemResellerItem->bechlem_printer->brand;

                    $partnr = '';

                    if (
                        is_array($bechlemResellerItem->bechlem_printer->bechlem_identifiers) &&
                        !empty($bechlemResellerItem->bechlem_printer->bechlem_identifiers)
                    ) {
                        foreach ($bechlemResellerItem->bechlem_printer->bechlem_identifiers as $bechlemIdentifier) {
                            if (($bechlemIdentifier->id_type === 'PARTNR') && !empty($bechlemIdentifier->sync_id) && empty($partnr)) {
                                $partnr .= $bechlemIdentifier->sync_id;
                            } elseif (($bechlemIdentifier->id_type === 'PARTNR') && !empty($bechlemIdentifier->sync_id) && !empty($partnr)) {
                                $partnr .= ',' . $bechlemIdentifier->sync_id;
                            }

                            if (($bechlemIdentifier->id_type === 'ARTNR') && !empty($bechlemIdentifier->sync_id) && empty($manufacturerSku)) {
                                $manufacturerSku .= $bechlemIdentifier->sync_id;
                            } elseif (($bechlemIdentifier->id_type === 'ARTNR') && !empty($bechlemIdentifier->sync_id) && !empty($manufacturerSku)) {
                                $manufacturerSku .= ',' . $bechlemIdentifier->sync_id;
                            }

                            if (($bechlemIdentifier->id_type === 'EAN') && !empty($bechlemIdentifier->sync_id) && empty($ean)) {
                                $ean .= $bechlemIdentifier->sync_id;
                            } elseif (($bechlemIdentifier->id_type === 'EAN') && !empty($bechlemIdentifier->sync_id) && !empty($ean)) {
                                $ean .= ',' . $bechlemIdentifier->sync_id;
                            }
                        }
                    }

                    $productNameWithManufacturer = $bechlemResellerItem->bechlem_printer->brand
                        . ' '
                        . $bechlemResellerItem->bechlem_printer->printer_series
                        . ' '
                        . $bechlemResellerItem->bechlem_printer->name;
                    if (!empty($bechlemResellerItem->bechlem_printer->art_nr)) {
                        $productNameWithManufacturer .= ' - '
                            . $bechlemResellerItem->bechlem_printer->art_nr;
                    }

                    if (!empty($partnr)) {
                        $productNameWithManufacturer = $bechlemResellerItem->bechlem_printer->brand
                            . ' '
                            . $partnr
                            . ' '
                            . $bechlemResellerItem->bechlem_printer->name;
                        if (!empty($manufacturerSku)) {
                            $productNameWithManufacturer .= ' - '
                                . $manufacturerSku;
                        }
                    }

                    if (!empty($bechlemResellerItem->bechlem_printer->printer_series)) {
                        $shortDescription = $bechlemResellerItem->bechlem_printer->printer_series;
                    }

                    $productTypeId = $bechlemResellerItem->bechlem_printer->id_category;
                    $productTypeName = $bechlemResellerItem->bechlem_printer->category;

                    $image = $bechlemResellerItem->bechlem_printer->picture;
                }

                if (!empty($ean)) {
                    $bechlemResellerItem->ean = $ean;
                }

                if (!empty($manufacturerSku)) {
                    $bechlemResellerItem->oem_nr = $manufacturerSku;
                }

                $connection->insert('bechlem_products', [
                    'uuid_id'                           => Text::uuid(),
                    'bechlem_id'                        => trim($bechlemResellerItem->id_item),
                    'ean'                               => trim($bechlemResellerItem->ean),
                    'manufacturer_sku'                  => trim($bechlemResellerItem->oem_nr),
                    'your_sku'                          => trim($bechlemResellerItem->id_art_nr),
                    'manufacturer_id'                   => trim($manufacturerId),
                    'manufacturer_name'                 => trim($manufacturerName),
                    'product_name_with_manufacturer'    => trim($productNameWithManufacturer),
                    'short_description'                 => trim($bechlemResellerItem->description),
                    'product_type_id'                   => trim($productTypeId),
                    'product_type_name'                 => trim($productTypeName),
                    'image'                             => trim($image),
                    'created'                           => new DateTime('now'),
                    'modified'                          => new DateTime('now'),
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
