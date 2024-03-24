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
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Setting component
 *
 * Class SettingComponent
 * @package BechlemConnectLight\Controller\Component
 */
class SettingComponent extends Component
{
    /**
     * Locales for layout
     *
     * @var array
     * @access public
     */
    public array $settingsForLayout = [];

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

        // Set the locales for layout in the $localesForLayout array
        $this->setSettingsForLayout();

        // Set the locales for layout as variable for the view
        $Controller->set('settings_for_layout', $this->settingsForLayout);
    }

    /**
     * Set settings for layout
     *
     * Settings will be available in this variable in views: $settings_for_layout
     *
     * @return void
     */
    public function setSettingsForLayout()
    {
        // Get the default settings which are null and not domain related
        $defaultSettings = $this->getDefaultSettings();

        // Get domain related settings by domain id
        $domainSettings = $this->getDomainSettings();

        // Merge the default with the domain related into global settings
        $globalSettings = Hash::merge($defaultSettings, $domainSettings);

        foreach ($globalSettings as $setting) {
            $this->settingsForLayout = Hash::insert(
                $this->settingsForLayout,
                'settings' . '.' . $setting->name,
                $setting->value
            );
        }
    }

    /**
     * Get default settings method
     *
     * @return array
     */
    private function getDefaultSettings()
    {
        $Settings = TableRegistry::getTableLocator()->get('BechlemConnectLight.Settings');
        $defaultSettings = $Settings
            ->find('all')
            ->where(function ($exp, $q) {
                return $exp
                    ->isNull('domain_id')
                    ->like('parameter', 'site');
            })
            ->orderBy(['Settings.name' => 'ASC'])
            ->toArray();

        return $defaultSettings;
    }

    /**
     * Get domain settings method
     *
     * @return array
     */
    private function getDomainSettings()
    {
        $domainSettings = [];
        if (!empty($this->domainId)) {
            // Get domain related settings by domain id
            $Settings = TableRegistry::getTableLocator()->get('BechlemConnectLight.Settings');
            $domainSettings = $Settings
                ->find('all')
                ->where(['domain_id' => $this->domainId])
                ->orderBy(['Settings.name' => 'ASC'])
                ->toArray();
        }

        return $domainSettings;
    }
}
