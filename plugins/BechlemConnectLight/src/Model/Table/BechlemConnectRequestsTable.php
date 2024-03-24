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

use BechlemConnectLight\Network\NetworkTrait;
use Cake\Log\Log;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Cake\Http\Client;
use Exception;
use Psr\Log\LogLevel;

/**
 * BechlemConnectRequests Model
 *
 * @property \Cake\ORM\Association\BelongsTo $BechlemConnectConfigs
 *
 * @method \BechlemConnectLight\Model\Entity\BechlemConnectRequest get($primaryKey, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemConnectRequest newEntity($data = null, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemConnectRequest[] newEntities(array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemConnectRequest|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemConnectRequest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemConnectRequest[] patchEntities($entities, array $data, array $options = [])
 * @method \BechlemConnectLight\Model\Entity\BechlemConnectRequest findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BechlemConnectRequestsTable extends Table
{

    use NetworkTrait;

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

        $this->setTable('bechlem_connect_requests');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('BechlemConnectLight.Trackable');
        $this->addBehavior('BechlemConnectLight.Deletable');

        $this->belongsTo('BechlemConnectConfigs', [
            'foreignKey' => 'bechlem_connect_config_id',
            'className' => 'BechlemConnectLight.BechlemConnectConfigs'
        ]);

        // Setup search filter using search manager
        $this->searchManager()
            ->value('config', [
                'fields' => ['BechlemConnectConfigs.alias']
            ])
            ->value('method', [
                'fields' => ['method']
            ])
            ->add('search', 'Search.Like', [
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'fields' => [
                    'name',
                    'slug',
                    'method',
                    'url',
                    'language',
                    'data',
                    'options',
                    'description',
                    'example',
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
        'bechlem_connect_config_id',
        'name',
        'slug',
        'method',
        'url',
        'language',
        'data',
        'options',
        'description',
        'example',
        'log',
        'status',
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
            ->requirePresence('name', 'create')
            ->notBlank('name');

        $validator
            ->requirePresence('slug', 'create')
            ->notBlank('slug');

        $validator
            ->requirePresence('method', 'create')
            ->notBlank('method');

        $validator
            ->requirePresence('url', 'create')
            ->notBlank('url');

        $validator
            ->allowEmptyString('language');

        $validator
            ->allowEmptyString('data');

        $validator
            ->allowEmptyString('options');

        $validator
            ->allowEmptyString('description');

        $validator
            ->allowEmptyString('example');

        $validator
            ->boolean('log')
            ->requirePresence('log', 'create')
            ->notBlank('log');

        $validator
            ->boolean('status')
            ->requirePresence('status', 'create')
            ->notBlank('status');

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
        $rules->add($rules->existsIn(['bechlem_connect_config_id'], 'BechlemConnectConfigs'));

        return $rules;
    }

    /**
     * Run request method.
     *
     * @param object|null $controller
     * @param object|null $bechlemConnectRequest
     *
     * @return array|string
     */
    public function runRequest(object $controller = null, object $bechlemConnectRequest = null)
    {
        $response = [];
        try {
            $response = $this->httpCall($bechlemConnectRequest);
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);
        }

        if ($bechlemConnectRequest->log == 1) {

            if (!empty($bechlemConnectRequest->data) && empty($bechlemConnectRequest->language) && empty($bechlemConnectRequest->options)) { // data
                $request = $bechlemConnectRequest->bechlem_connect_config->scheme . '://'
                    . $bechlemConnectRequest->bechlem_connect_config->host
                    . $bechlemConnectRequest->url . '?' . 'table='
                    . $bechlemConnectRequest->data;
            } elseif (!empty($bechlemConnectRequest->data) && empty($bechlemConnectRequest->language) && !empty($bechlemConnectRequest->options)) { // data + options
                $request = $bechlemConnectRequest->bechlem_connect_config->scheme . '://'
                    . $bechlemConnectRequest->bechlem_connect_config->host
                    . $bechlemConnectRequest->url . '?' . 'table='
                    . $bechlemConnectRequest->data . '&'
                    . $bechlemConnectRequest->options;
            } elseif (!empty($bechlemConnectRequest->data) && !empty($bechlemConnectRequest->language) && empty($bechlemConnectRequest->options)) { // data + language
                $request = $bechlemConnectRequest->bechlem_connect_config->scheme . '://'
                    . $bechlemConnectRequest->bechlem_connect_config->host
                    . $bechlemConnectRequest->url . '?' . 'table='
                    . $bechlemConnectRequest->data . '&' . 'lang='
                    . $bechlemConnectRequest->language;
            } elseif (!empty($bechlemConnectRequest->data) && !empty($bechlemConnectRequest->language) && !empty($bechlemConnectRequest->options)) { // data + language + options
                $request = $bechlemConnectRequest->bechlem_connect_config->scheme . '://'
                    . $bechlemConnectRequest->bechlem_connect_config->host
                    . $bechlemConnectRequest->url . '?' . 'table='
                    . $bechlemConnectRequest->data . '&' . 'lang='
                    . $bechlemConnectRequest->language . '&'
                    . $bechlemConnectRequest->options;
            }

            $this->requestLog(
                $controller,
                'httpCall',
                $request,
                $response
            );
        }

        return $response;
    }

    /**
     * Http call method
     *
     * @param object|null $bechlemConnectRequest
     *
     * @return array|string
     */
    public function httpCall(object $bechlemConnectRequest = null)
    {
        $data = [];
        try {

            if (!empty($bechlemConnectRequest->data) && empty($bechlemConnectRequest->language) && empty($bechlemConnectRequest->options)) { // data
                $request = $bechlemConnectRequest->bechlem_connect_config->scheme . '://'
                    . $bechlemConnectRequest->bechlem_connect_config->host
                    . $bechlemConnectRequest->url . '?' . 'table='
                    . $bechlemConnectRequest->data;
            } elseif (!empty($bechlemConnectRequest->data) && empty($bechlemConnectRequest->language) && !empty($bechlemConnectRequest->options)) { // data + options
                $request = $bechlemConnectRequest->bechlem_connect_config->scheme . '://'
                    . $bechlemConnectRequest->bechlem_connect_config->host
                    . $bechlemConnectRequest->url . '?' . 'table='
                    . $bechlemConnectRequest->data . '&'
                    . $bechlemConnectRequest->options;
            } elseif (!empty($bechlemConnectRequest->data) && !empty($bechlemConnectRequest->language) && empty($bechlemConnectRequest->options)) { // data + language
                $request = $bechlemConnectRequest->bechlem_connect_config->scheme . '://'
                    . $bechlemConnectRequest->bechlem_connect_config->host
                    . $bechlemConnectRequest->url . '?' . 'table='
                    . $bechlemConnectRequest->data . '&' . 'lang='
                    . $bechlemConnectRequest->language;
            } elseif (!empty($bechlemConnectRequest->data) && !empty($bechlemConnectRequest->language) && !empty($bechlemConnectRequest->options)) { // data + language + options
                $request = $bechlemConnectRequest->bechlem_connect_config->scheme . '://'
                    . $bechlemConnectRequest->bechlem_connect_config->host
                    . $bechlemConnectRequest->url . '?' . 'table='
                    . $bechlemConnectRequest->data . '&' . 'lang='
                    . $bechlemConnectRequest->language . '&'
                    . $bechlemConnectRequest->options;
            }

            $http = new Client();
            $response = $http->get($request, [], ['timeout' => 900]);

            $csv = 'data.csv';
            $handle = fopen(TMP . $csv, 'w');
            fwrite($handle , $response->getStringBody());
            fclose($handle);

            $data = $this->csvToArray(TMP . $csv, ';', '"');
            unlink(TMP . $csv);

        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, (string)$ex);
        }

        return $data;
    }

    /**
     * Csv to array
     *
     * @param string $fileName
     * @param string $delimiter
     * @param string $enclosure
     *
     * @return array|null
     */
    public function csvToArray(string $fileName, string $delimiter = ',', string $enclosure = '"')
    {
        $array = [];
        $rowCount = 0;
        if (($handle = fopen($fileName, 'r')) !== false) {
            $maxLineLength = defined('MAX_LINE_LENGTH')? MAX_LINE_LENGTH: 10000;
            $header = fgetcsv($handle, $maxLineLength, $delimiter, $enclosure);
            // Clean array keys for CakePHP conventions
            $keys = [];
            foreach ($header as $key) {
                $keys[] = Text::slug(strtolower($key), '_');
            }
            $headerColCount = count($header);
            while (($row = fgetcsv($handle, $maxLineLength, $delimiter, $enclosure)) !== false) {
                $rowColCount = count($row);
                if ($rowColCount == $headerColCount) {
                    // Combine keys and vals
                    $entry = array_combine($keys, $row);
                    $array[] = $entry;
                }
                $rowCount++;
            }
            fclose($handle);
        } else {
            Log::write(
                LogLevel::ERROR,
                __d(
                    'bechlem_connect_light',
                    'Could not read {fileName}',
                    ['fileName' => $fileName]
                )
            );

            return null;
        }

        return $array;
    }
}
