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
namespace BechlemConnectLight\Controller\Admin;

use BechlemConnectLight\Controller\Admin\AppController;
use Cake\ORM\TableRegistry;

/**
 * Dashboards Controller
 *
 */
class DashboardsController extends AppController
{
    /**
     * Initialization hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @return void
     */
    public function initialize($loadComponents = true): void
    {
        parent::initialize(true);
    }

    /**
     * Dashboard method
     *
     * @return void
     */
    public function dashboard()
    {
        $bechlemConnectDemoData = 0;
        $bechlemConnectConfigConnectData = [];

        $BechlemConnectConfigs = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemConnectConfigs');
        $bechlemConnectConfig = $BechlemConnectConfigs
            ->find()
            ->where([
                'BechlemConnectConfigs.alias'       => 'datawriter',
                'BechlemConnectConfigs.username'    => 'www.datawriter.de',
                'BechlemConnectConfigs.password'    => 'www.datawriter.de',
                'BechlemConnectConfigs.status'      => 1,
            ])
            ->first();
        if (!empty($bechlemConnectConfig->id)) {
            $bechlemConnectDemoData = 1;
            $bechlemConnectConfigConnectData = $bechlemConnectConfig;
        }

        $BechlemProducts = TableRegistry::getTableLocator()->get('BechlemConnectLight.BechlemProducts');
        $bechlemProductManufacturersCount = $BechlemProducts
            ->find()
            ->distinct('BechlemProducts.manufacturer_id')
            ->count();
        $bechlemProductTypesCount = $BechlemProducts
            ->find()
            ->distinct('BechlemProducts.product_type_id')
            ->count();

        $bechlemProductManufacturersQuery = $BechlemProducts->find('all');
        $bechlemProductManufacturers = $bechlemProductManufacturersQuery
            ->select([
                'BechlemProducts.manufacturer_name',
                'manufacturer_product_count' => $bechlemProductManufacturersQuery->func()->count('BechlemProducts.id'),
            ])
            ->orderBy(['BechlemProducts.manufacturer_id' => 'ASC'])
            ->distinct(['BechlemProducts.manufacturer_id']);

        $bechlemProductTypesQuery = $BechlemProducts->find('all');
        $bechlemProductTypes = $bechlemProductTypesQuery
            ->select([
                'BechlemProducts.product_type_name',
                'type_product_count' => $bechlemProductTypesQuery->func()->count('BechlemProducts.id'),
            ])
            ->orderBy(['BechlemProducts.product_type_id' => 'ASC'])
            ->distinct(['BechlemProducts.product_type_id']);

        $Domains = TableRegistry::getTableLocator()->get('BechlemConnectLight.Domains');
        $domainsCount = $Domains
            ->find()
            ->count();

        $Locales = TableRegistry::getTableLocator()->get('BechlemConnectLight.Locales');
        $localesCount = $Locales
            ->find()
            ->where(['status' => 1])
            ->count();

        $Countries = TableRegistry::getTableLocator()->get('BechlemConnectLight.Countries');
        $countriesCount = $Countries
            ->find()
            ->where(['status' => 1])
            ->count();

        $Registrations = TableRegistry::getTableLocator()->get('BechlemConnectLight.Registrations');
        $registrationsCount = $Registrations
            ->find()
            ->count();

        $Roles = TableRegistry::getTableLocator()->get('BechlemConnectLight.Roles');
        $rolesCount = $Roles
            ->find()
            ->count();

        $Users = TableRegistry::getTableLocator()->get('BechlemConnectLight.Users');
        $usersCount = $Users
            ->find()
            ->where(['status' => 1])
            ->count();

        $UserProfiles = TableRegistry::getTableLocator()->get('BechlemConnectLight.UserProfiles');
        $userProfilesCount = $UserProfiles
            ->find()
            ->where(['status' => 1])
            ->count();

        $this->set(compact(
            'domainsCount',
            'localesCount',
            'countriesCount',
            'registrationsCount',
            'rolesCount',
            'usersCount',
            'userProfilesCount',
            'bechlemProductManufacturersCount',
            'bechlemProductManufacturers',
            'bechlemProductTypesCount',
            'bechlemProductTypes',
            'bechlemConnectDemoData',
            'bechlemConnectConfigConnectData',
        ));
    }
}
