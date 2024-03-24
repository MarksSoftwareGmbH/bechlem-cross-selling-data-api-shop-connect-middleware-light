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
namespace BechlemConnectLight\Controller;

use App\Controller\AppController as BaseController;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Exception;
use Migrations\Migrations;
use BechlemConnectLight\Controller\Traits\HookableComponentTrait;
use BechlemConnectLight\Configure\Engine\SettingsConfig;

class AppController extends BaseController
{

    use HookableComponentTrait;

    /**
     * Http host
     *
     * @var string
     */
    private string $httpHost;

    /**
     * Domain id
     *
     * @var int
     */
    private int $domainId;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize($loadComponents = true): void
    {
        parent::initialize();

        // Set http host by environment
        if (filter_var(env('HTTP_HOST'), FILTER_VALIDATE_IP) !== false) {
            $this->httpHost = env('HTTP_X_FORWARDED_HOST');
        } else {
            $this->httpHost = env('HTTP_HOST');
        }

        // Deploy the Migrations based on the "default" datasource
        try {
            $connectionDefault = ConnectionManager::get('default');
            if ($connectionDefault) {

                /*
                 * When using proxies or load balancers, SSL/TLS connections might
                 * get terminated before reaching the server. If you trust the proxy,
                 * you can enable `$trustProxy` to rely on the `X-Forwarded-Proto`
                 * header to determine whether to generate URLs using `https`.
                 *
                 * See also https://book.cakephp.org/4/en/controllers/request-response.html#trusting-proxy-headers
                 */
                $trustProxy = false;

                $s = null;
                if (env('HTTPS') || ($trustProxy && env('HTTP_X_FORWARDED_PROTO') === 'https')) {
                    $s = 's';
                }

                $dateTime = DateTime::now();

                $connectionDefaultResults = $connectionDefault->execute('SELECT id, url, theme FROM domains')->fetchAll('assoc');
                if (empty($connectionDefaultResults)) {
                    $connectionDefault->execute('TRUNCATE TABLE domains');
                    $connectionDefault->insert('domains', [
                        'uuid_id'   => Text::uuid(),
                        'scheme'    => 'http' . $s,
                        'url'       => $this->httpHost,
                        'name'      => 'Bechlem Connect Light',
                        'theme'     => 'BechlemConnectLight',
                        'created'   => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                        'modified'  => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                    ], [
                        'created'   => 'datetime',
                        'modified'  => 'datetime',
                    ]);
                } else {
                    foreach ($connectionDefaultResults as $connectionDefaultResult) {
                        if (
                            ($connectionDefaultResult['url'] === 'bechlem-connect-light-github.tld') &&
                            ($connectionDefaultResult['theme'] === 'BechlemConnectLight')
                        ) {
                            $connectionDefault->update('domains', [
                                'uuid_id'   => Text::uuid(),
                                'scheme'    => 'http' . $s,
                                'url'       => $this->httpHost,
                                'modified'  => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                            ], ['id' => $connectionDefaultResult['id']]);
                        }
                    }
                }
            }

        } catch (Exception $e) {
            $migrations = new Migrations();
            $migrationsStatus = $migrations->status(['connection' => 'default', 'plugin' => 'BechlemConnectLight']);
            if (is_array($migrationsStatus) && !empty($migrationsStatus)) {

                $migrationsMigrate = $migrations->migrate(['connection' => 'default', 'plugin' => 'BechlemConnectLight']);
                if ($migrationsMigrate) {

                    $migrationsSeed = $migrations->seed(['connection' => 'default', 'plugin' => 'BechlemConnectLight']);
                    if ($migrationsSeed) {

                        $connectionDefault = ConnectionManager::get('default');
                        if ($connectionDefault) {

                            /*
                            * When using proxies or load balancers, SSL/TLS connections might
                            * get terminated before reaching the server. If you trust the proxy,
                            * you can enable `$trustProxy` to rely on the `X-Forwarded-Proto`
                            * header to determine whether to generate URLs using `https`.
                            *
                            * See also https://book.cakephp.org/4/en/controllers/request-response.html#trusting-proxy-headers
                            */
                            $trustProxy = false;

                            $s = null;
                            if (env('HTTPS') || ($trustProxy && env('HTTP_X_FORWARDED_PROTO') === 'https')) {
                                $s = 's';
                            }

                            $dateTime = DateTime::now();

                            $connectionDefaultResults = $connectionDefault->execute('SELECT  id, url, theme FROM domains')->fetchAll('assoc');
                            if (empty($connectionDefaultResults)) {
                                $connectionDefault->execute('TRUNCATE TABLE domains');
                                $connectionDefault->insert('domains', [
                                    'uuid_id'   => Text::uuid(),
                                    'scheme'    => 'http' . $s,
                                    'url'       => $this->httpHost,
                                    'name'      => 'Bechlem Connect Light',
                                    'theme'     => 'BechlemConnectLight',
                                    'created'   => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                    'modified'  => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                ], [
                                    'created'   => 'datetime',
                                    'modified'  => 'datetime',
                                ]);
                            } else {
                                foreach ($connectionDefaultResults as $connectionDefaultResult) {
                                    if (
                                        ($connectionDefaultResult['url'] === 'bechlem-connect-light-github.tld') &&
                                        ($connectionDefaultResult['theme'] === 'BechlemConnectLight')
                                    ) {
                                        $connectionDefault->update('domains', [
                                            'uuid_id'   => Text::uuid(),
                                            'scheme'    => 'http' . $s,
                                            'url'       => $this->httpHost,
                                            'modified'  => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                                        ], ['id' => $connectionDefaultResult['id']]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } 

        // Define Datasource Connection
        if (strpos($this->httpHost, 'localhost') !== false) {
            // Development environment
            ConnectionManager::drop('default');
            ConnectionManager::setConfig('default', ConnectionManager::get('development'));
        } elseif (PHP_SAPI === 'cli') {
            // Define Datasource Connection for unit testing and cli commands
            // Default default or other environment
            ConnectionManager::drop('default');
            ConnectionManager::setConfig('default', ConnectionManager::get('default'));
        } else {
            // Default production or other environment
            ConnectionManager::drop('default');
            ConnectionManager::setConfig('default', ConnectionManager::get('production'));
        }

        // Set domain id by http host
        $Domains = TableRegistry::getTableLocator()->get('BechlemConnectLight.Domains');
        $domain = $Domains
            ->find()
            ->where(['url' => $this->httpHost])
            ->first();
        if (!empty($domain)) {
            $this->domainId = $domain->id;
        }

        if ($loadComponents) {
            if (PHP_SAPI !== 'cli') {
                $this->loadComponent('FormProtection');
            }

            $this->loadComponent('Search.Search', [
                'actions' => ['index', 'indexCards', 'search', 'view'],
            ]);

            // Load all Components
            $this->_dispatchAfterInitialize();

            if (
                in_array(
                    $this->getRequest()->getParam('prefix'),
                    ['Admin']
                ) ||
                in_array(
                    $this->getRequest()->getParam('action'),
                    [
                        'login',
                        'logout',
                        'forgot',
                        'reset',
                        'profile',
                    ]
                )
            ) {
                /**
                 * Add a component to the controller's registry.
                 *
                 * This method will also set the component to a property.
                 * For example:
                 *
                 * ```
                 * $this->loadComponent('Acl.Acl');
                 * ```
                 *
                 * Will result in a `Toolbar` property being set.
                 *
                 * @param string $name The name of the component to load.
                 * @param array $config The config for the component.
                 * @return \Cake\Controller\Component
                 */
                $this->loadComponent('TinyAuth.Auth', [
                    'loginAction' => [
                        'plugin' => 'BechlemConnectLight',
                        'controller' => 'Users',
                        'action' => 'login',
                    ],
                    'loginRedirect' => [
                        'prefix' => 'Admin',
                        'plugin' => 'BechlemConnectLight',
                        'controller' => 'Dashboards',
                        'action' => 'dashboard',
                    ],
                    'logoutRedirect' => [
                        'plugin' => 'BechlemConnectLight',
                        'controller' => 'Users',
                        'action' => 'login',
                    ],
                    'authError' => __d('bechlem_connect_light', 'You are not authorized to access this location.'),
                    'flash' => [
                        'element' => 'default',
                        'params' => ['class' => 'error'],
                    ],
                    'allowFilePath' => Plugin::configPath('BechlemConnectLight'),
                    'autoClearCache' => true,
                    'authenticate' => [
                        'TinyAuth.Form' => [
                            'fields' => [
                                'username' => 'username',
                                'password' => 'password',
                            ],
                            'userModel' => 'BechlemConnectLight.Users',
                            // Use finder method in users table
                            'finder' => 'auth',
                        ],
                    ],
                    'authorize' => [
                        'TinyAuth.Tiny' => [
                            'superAdminRole' => '1', // id of super admin role, which grants access to ALL resources
                            'superAdmin' => '1', // super admin, which grants access to ALL resources
                            'pivotTable' => 'roles_users', // Should be used in multi-roles setups
                            'aclFilePath' => Plugin::configPath('BechlemConnectLight'), // Possible to locate INI file at given path e.g. Plugin::configPath('Admin'), filePath is also available for shared config
                            'rolesTable' => 'BechlemConnectLight.Roles', // name of Configure key holding available roles OR class name of roles table
                            'usersTable' => 'BechlemConnectLight.Users', // name of the Users table
                            'autoClearCache' => true, // Set to true to delete cache automatically in debug mode, keep null for auto-detect
                        ],
                    ],
                ]);
            }
        }
    }

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
        /**
         * Parent before filter callback.
         *
         * @param \Cake\Event\Event $event The beforeFilter event.
         * @return void
         */
        parent::beforeFilter($event);

        /**
         * Load Settings
         */
        Configure::config('settings', new SettingsConfig(null, $this->domainId));
        Configure::load('settings', 'settings');
    }

    /**
     * Called after the controller action is run, but before the view is rendered. You can use this method
     * to perform logic or set view variables that are required on every request.
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     * @link https://book.cakephp.org/4/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeRender(EventInterface $event)
    {
        /**
         * Parent before render callback.
         *
         * @param \Cake\Event\Event $event The beforeRender event.
         * @return void
         */
        parent::beforeRender($event);

        /**
         * The view theme to use.
         *
         * @param string|null|false $theme Theme name. If null returns current theme.
         *   Use false to remove the current theme.
         * @return string|$this
         */
        if (in_array($this->getRequest()->getParam('prefix'), ['admin'])) {
            // BechlemConnectLight default backend theme
            $backendTheme = 'BechlemConnectLight';

            // Plugin based custom backend theme
            if (Configure::check('BechlemConnectLight.settings.backendTheme')) {
                $backendTheme = Configure::read('BechlemConnectLight.settings.backendTheme');
            }

            $this
                ->viewBuilder()
                ->setTheme($backendTheme);
        } else {
            // BechlemConnectLight default frontend theme
            $frontendTheme = 'BechlemConnectLight';

            // Plugin based custom frontend theme
            if (Configure::check('BechlemConnectLight.settings.frontendTheme')) {
                $frontendTheme = Configure::read('BechlemConnectLight.settings.frontendTheme');
            }

            $this
                ->viewBuilder()
                ->setTheme($frontendTheme);
        }

        /**
         * Adds helpers to use by merging with existing ones.
         *
         * @param array $helpers Helpers to use.
         * @return $this
         * @since 4.3.0
         */
        $this
            ->viewBuilder()
            ->addHelpers([
                // Extending the default Helper classes
                'Html' => [
                    'className' => 'BootstrapUI.Html',
                    'iconDefaults' => [
                        'tag' => 'i',
                        'namespace' => 'fas',
                        'prefix' => 'fa',
                    ],
                ],
                'Form' => ['className' => 'BootstrapUI.Form'],
                'Paginator' => ['className' => 'BootstrapUI.Paginator'],
                'Breadcrumbs' => ['className' => 'BootstrapUI.Breadcrumbs'],
                // BechlemConnectLight global custom view helper methods
                'BechlemConnectLight' => ['className' => 'BechlemConnectLight.BechlemConnectLight'],
                'Menu' => ['className' => 'BechlemConnectLight.Menu'],
            ]);
    }
}
