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
namespace BechlemConnectLight\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Locale component
 *
 * Class LocaleComponent
 * @package BechlemConnectLight\Controller\Component
 */
class LocaleComponent extends Component
{
    /**
     * Locales for layout
     *
     * @var array
     * @access public
     */
    public array $localesForLayout = [];

    /**
     * Default config
     *
     * These are merged with user-provided config when the component is used.
     *
     * @var array
     */
    protected array $defaultConfig = [];

    /**
     * Http host
     *
     * @var string
     */
    private string $httpHost;

    /**
     * Domain id
     *
     * @var string
     */
    private int $domainId;

    /**
     * Constructor hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @param array $config The configuration settings provided to this component.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    /**
     * Is called before the controller’s beforeFilter method, but after the
     * controller’s initialize() method.
     *
     * @param \Cake\Event\Event $event Event instance.
     */
    public function beforeFilter(Event $event)
    {
        $Controller = $event->getSubject();

        // Set http host by environment
        if (filter_var(env('HTTP_HOST'), FILTER_VALIDATE_IP) !== false) {
            $this->httpHost = env('HTTP_X_FORWARDED_HOST');
        } else {
            $this->httpHost = env('HTTP_HOST');
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

        // Set the locales for layout
        $this->setLocalesForLayout();

        // Check if locale code is already in session if not take the browsers accept language
        if (!$Controller->getRequest()->getSession()->check('Locale.code')) {
            // Check users browser 1st favorite language
            $code = 'en';
            if (!empty(getEnv('HTTP_ACCEPT_LANGUAGE'))) {
                $code = substr(getEnv('HTTP_ACCEPT_LANGUAGE'), 0, 2);
            }
            if ($code) {
                $code = $this->processLocaleForSession($Controller, $code);
            } else {
                $code = $this->processLocaleForSession($Controller);
            }
        } else {
            $code = $Controller->getRequest()->getSession()->read('Locale.code');
        }

        // Check if locale code is a request param
        if ($Controller->getRequest()->getParam('locale')) {
            $code = $this->processLocaleForSession($Controller, $Controller->getRequest()->getParam('locale'));
        }

        // Change the locale at runtime
        I18n::setLocale($code);

        // Set the locales for layout as variable for the view
        $event->getSubject()->set('locales_for_layout', $this->localesForLayout);
    }

    /**
     * Set locales for layout
     *
     * Locales will be available in this variable in views: $locales_for_layout
     *
     * @return void
     */
    public function setLocalesForLayout()
    {
        $Locales = TableRegistry::getTableLocator()->get('BechlemConnectLight.Locales');
        $locales = $Locales
            ->find('all')
            ->matching('Domains', function ($q) {
                return $q->where(['Domains.id' => $this->domainId]);
            })
            ->where(['Locales.status' => 1])
            ->orderBy(['Locales.weight' => 'ASC'])
            ->toArray();

        $this->localesForLayout = Hash::insert($this->localesForLayout, 'locales', $locales);
    }

    /**
     * Process locale for session
     *
     * Locale will be written into session
     *
     * @param object $controller
     * @param string $code
     * @param string $name
     * @return string
     */
    public function processLocaleForSession(object $controller, $code = 'en_US', $name = 'English')
    {
        $Locales = TableRegistry::getTableLocator()->get('BechlemConnectLight.Locales');
        $locale = $Locales
            ->find()
            ->matching('Domains', function ($q) {
                return $q
                    ->where(['Domains.id' => $this->domainId]);
            })
            ->where([
                'Locales.code' => $code,
                'Locales.status' => 1
            ])
            ->select(['code', 'name'])
            ->first();

        if (strlen($code) == 2) {
            $locale = $Locales
                ->find()
                ->matching('Domains', function ($q) {
                    return $q
                        ->where(['Domains.id' => $this->domainId]);
                })
                ->where([
                    'Locales.code LIKE' => '%' . $code . '%',
                    'Locales.status' => 1,
                ])
                ->select(['code', 'name'])
                ->first();
        }

        if ($locale) {
            $code = $locale->code;
            $name = $locale->name;
        } else {
            $code = 'en_US';
            $name = 'English';
        }

        // Save the changed locale into session at runtime
        $controller->getRequest()->getSession()->write('Locale.code', $code);
        $controller->getRequest()->getSession()->write('Locale.name', $name);

        return $code;
    }
}
