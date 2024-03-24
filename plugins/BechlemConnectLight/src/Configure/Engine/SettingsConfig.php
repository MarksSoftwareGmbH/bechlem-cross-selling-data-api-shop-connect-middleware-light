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
namespace BechlemConnectLight\Configure\Engine;

use Cake\Core\Configure\ConfigEngineInterface;
use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Class SettingsConfig
 *
 * @package BechlemConnectLight\Configure\Engine
 */
class SettingsConfig implements ConfigEngineInterface
{

    /**
     * Table name
     *
     * @var object
     */
    private object $_table;

    /**
     * Domain id
     *
     * @var int
     */
    private int $_domainId;

    /**
     * SettingsConfig constructor.
     *
     * @param \Cake\ORM\Table|null $table
     * @param int|null $domainId
     */
    public function __construct(Table $table = null, int $domainId = null)
    {
        if (!$table) {
            $table = TableRegistry::getTableLocator()->get('BechlemConnectLight.Settings');
        }

        $this->_table = $table;
        $this->_domainId = $domainId;
    }

    /**
     * Read a configuration file/storage key
     *
     * This method is used for reading configuration information from sources.
     * These sources can either be static resources like files, or dynamic ones like
     * a database, or other datasource.
     *
     * @param string $key Key to read.
     * @return array An array of data to merge into the runtime configuration
     */
    public function read(string $key): array
    {
        // Get the default settings which are null and not domain related
        $defaultSettings = $this->getDefaultSettings();

        // Get domain related settings by domain id
        $domainSettings = $this->getDomainSettings();

        // Merge the default with the domain related into global settings
        $globalSettings = Hash::merge($defaultSettings, $domainSettings);

        $config = [];
        foreach ($globalSettings as $setting) {
            $config = Hash::insert($config, 'BechlemConnectLight' . '.' . $key . '.' . $setting->name, $setting->value);
        }

        return $config;
    }

    /**
     * Dumps the configure data into the storage key/file of the given `$key`.
     *
     * @param string $key The identifier to write to.
     * @param array $data The data to dump.
     * @return bool True on success or false on failure.
     */
    public function dump(string $key, array $data): bool
    {
        Log::debug($key);
        Log::debug($data);

        return true;
    }

    /**
     * Get default settings method
     *
     * @return array
     */
    private function getDefaultSettings()
    {
        $defaultSettings = $this->_table
            ->find('all')
            ->where(function ($exp, $q) {
                return $exp->isNull('domain_id');
            })
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
        if (!empty($this->_domainId)) {
            // Get domain related settings by domain id
            $domainSettings = $this->_table
                ->find('all')
                ->where(['domain_id' => $this->_domainId])
                ->toArray();
        }

        // Get the domain theme
        $domainTheme = $this->getDomainTheme();
        if (!empty($domainTheme)) {
            // Merge the domain related settings with the domain theme, frontendTheme as name is essential
            $domainFrontendTheme = [(object)['name' => 'frontendTheme', 'value' => $domainTheme]];
            $domainSettings = Hash::merge($domainSettings, $domainFrontendTheme);
        }

        return $domainSettings;
    }

    /**
     * Get domain theme method
     *
     * @return string|null
     */
    private function getDomainTheme()
    {
        // Get domain theme by domain id
        $Domains = TableRegistry::getTableLocator()->get('BechlemConnectLight.Domains');
        $domain = $Domains
            ->find()
            ->select(['theme'])
            ->where(['id' => $this->_domainId])
            ->first();

        // Check if the theme is given and a plugin with the same name is loaded
        if (!empty($domain->theme)) {
            if (Configure::check('plugins' . '.' . $domain->theme)) {
                return $domain->theme;
            }
        }

        return null;
    }
}
