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
use Cake\Routing\RouteBuilder;

/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setExtensions(['json', 'xml', 'csv', 'txt', 'pdf']);

$routes->plugin('BechlemConnectLight', ['path' => '/'], function (RouteBuilder $routes) {

        // Switch locale
        $routes
            ->connect('/switch-locale/{code}', ['controller' => 'Locales', 'action' => 'switchLocale'])
            ->setPass(['code']);

        // User login
        $routes
            ->connect('/{locale}/', ['controller' => 'Users', 'action' => 'login'])
            ->setPatterns(['locale' => 'de|en|nl|fr|it|es|pt|ru|zh|ar|he|pl']);
        $routes
            ->connect('/', ['controller' => 'Users', 'action' => 'login']);
        // User register
        $routes
            ->connect('/register', ['controller' => 'Users', 'action' => 'register']);
        // User logout
        $routes
            ->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
        // User forgot
        $routes
            ->connect('/forgot', ['controller' => 'Users', 'action' => 'forgot']);
        // User reset
        $routes
            ->connect('/reset/{username}/{token}', ['controller' => 'Users', 'action' => 'reset'])
            ->setPass(['username', 'token'])
            ->setPatterns(['token' => '[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{12}']);

        // Registration
        $routes
            ->connect('/registration', ['controller' => 'Registrations', 'action' => 'add']);

        /*
         * Admin Prefix Routing
         */
        $routes->prefix('Admin', ['_namePrefix' => 'admin:'], function (RouteBuilder $routes) {
            $routes
                ->setExtensions(['ajax', 'json', 'xml', 'csv', 'txt', 'pdf']);

            // Switch locale
            $routes
                ->connect('/switch-locale/{code}', ['controller' => 'Locales', 'action' => 'switchLocale'])
                ->setPass(['code']);

            $routes
                ->connect('/app/clear-cache', ['controller' => 'AppCaches', 'action' => 'clearAppCaches']);
            $routes
                ->connect('/app/clear-log', ['controller' => 'AppLogs', 'action' => 'clearAppLogs']);
            $routes
                ->connect('/app/clear-session', ['controller' => 'AppSessions', 'action' => 'clearAppSessions']);

            /*
             * Bechlem Connect Configs Controller
             */
            $routes
                ->connect('/bechlem-connect-configs', ['controller' => 'BechlemConnectConfigs', 'action' => 'index']);
            $routes
                ->connect('/bechlem-connect-config/{id}', ['controller' => 'BechlemConnectConfigs', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-connect-config/add', ['controller' => 'BechlemConnectConfigs', 'action' => 'add']);
            $routes
                ->connect('/bechlem-connect-config/edit/{id}', ['controller' => 'BechlemConnectConfigs', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-connect-config/delete/{id}', ['controller' => 'BechlemConnectConfigs', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-connect-configs/import', ['controller' => 'BechlemConnectConfigs', 'action' => 'import']);
            $routes
                ->connect('/bechlem-connect-configs/export-xlsx', ['controller' => 'BechlemConnectConfigs', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-connect-configs/export-csv', ['controller' => 'BechlemConnectConfigs', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-connect-configs/export-xml', ['controller' => 'BechlemConnectConfigs', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-connect-configs/export-json', ['controller' => 'BechlemConnectConfigs', 'action' => 'exportJson']);

            /*
             * Bechlem Connect Requests Controller
             */
            $routes
                ->connect('/bechlem-connect-requests', ['controller' => 'BechlemConnectRequests', 'action' => 'index']);
            $routes
                ->connect('/bechlem-connect-request/{id}', ['controller' => 'BechlemConnectRequests', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-connect-request/add', ['controller' => 'BechlemConnectRequests', 'action' => 'add']);
            $routes
                ->connect('/bechlem-connect-request/edit/{id}', ['controller' => 'BechlemConnectRequests', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-connect-request/delete/{id}', ['controller' => 'BechlemConnectRequests', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-connect-request/copy/{id}', ['controller' => 'BechlemConnectRequests', 'action' => 'copy'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-connect-request/run/{id}', ['controller' => 'BechlemConnectRequests', 'action' => 'run'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-connect-requests/import', ['controller' => 'BechlemConnectRequests', 'action' => 'import']);
            $routes
                ->connect('/bechlem-connect-requests/export-xlsx', ['controller' => 'BechlemConnectRequests', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-connect-requests/export-csv', ['controller' => 'BechlemConnectRequests', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-connect-requests/export-xml', ['controller' => 'BechlemConnectRequests', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-connect-requests/export-json', ['controller' => 'BechlemConnectRequests', 'action' => 'exportJson']);

            /*
             * Bechlem Products Controller
             */
            $routes
                ->connect('/bechlem-products', ['controller' => 'BechlemProducts', 'action' => 'index']);
                $routes
                ->connect('/bechlem-products/cards', ['controller' => 'BechlemProducts', 'action' => 'indexCards']);
            $routes
                ->connect('/bechlem-product/{id}', ['controller' => 'BechlemProducts', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-products/update-all', ['controller' => 'BechlemProducts', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-product/load-data', ['controller' => 'BechlemProducts', 'action' => 'loadData']);
            $routes
                ->connect('/bechlem-products/export-xlsx', ['controller' => 'BechlemProducts', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-products/export-csv', ['controller' => 'BechlemProducts', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-products/export-xml', ['controller' => 'BechlemProducts', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-products/export-json', ['controller' => 'BechlemProducts', 'action' => 'exportJson']);

            /*
             * Bechlem Product Accessories Controller
             */
            $routes
                ->connect('/bechlem-product-accessories', ['controller' => 'BechlemProductAccessories', 'action' => 'index']);
            $routes
                ->connect('/bechlem-product-accessorie/{id}', ['controller' => 'BechlemProductAccessories', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-product-accessories/update-all', ['controller' => 'BechlemProductAccessories', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-product-accessories/export-xlsx', ['controller' => 'BechlemProductAccessories', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-product-accessories/export-csv', ['controller' => 'BechlemProductAccessories', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-product-accessories/export-xml', ['controller' => 'BechlemProductAccessories', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-product-accessories/export-json', ['controller' => 'BechlemProductAccessories', 'action' => 'exportJson']);

            /*
             * Bechlem Brands Controller
             */
            $routes
                ->connect('/bechlem-brands', ['controller' => 'BechlemBrands', 'action' => 'index']);
            $routes
                ->connect('/bechlem-brand/{id}', ['controller' => 'BechlemBrands', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-brands/update-all', ['controller' => 'BechlemBrands', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-brands/export-xlsx', ['controller' => 'BechlemBrands', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-brands/export-csv', ['controller' => 'BechlemBrands', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-brands/export-xml', ['controller' => 'BechlemBrands', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-brands/export-json', ['controller' => 'BechlemBrands', 'action' => 'exportJson']);

            /*
             * Bechlem Categories Controller
             */
            $routes
                ->connect('/bechlem-categories', ['controller' => 'BechlemCategories', 'action' => 'index']);
            $routes
                ->connect('/bechlem-category/{id}', ['controller' => 'BechlemCategories', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-categories/update-all', ['controller' => 'BechlemCategories', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-categories/export-xlsx', ['controller' => 'BechlemCategories', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-categories/export-csv', ['controller' => 'BechlemCategories', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-categories/export-xml', ['controller' => 'BechlemCategories', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-categories/export-json', ['controller' => 'BechlemCategories', 'action' => 'exportJson']);

            /*
             * Bechlem Identifiers Controller
             */
            $routes
                ->connect('/bechlem-identifiers', ['controller' => 'BechlemIdentifiers', 'action' => 'index']);
            $routes
                ->connect('/bechlem-identifier/{id}', ['controller' => 'BechlemIdentifiers', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-identifiers/update-all', ['controller' => 'BechlemIdentifiers', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-identifiers/export-xlsx', ['controller' => 'BechlemIdentifiers', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-identifiers/export-csv', ['controller' => 'BechlemIdentifiers', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-identifiers/export-xml', ['controller' => 'BechlemIdentifiers', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-identifiers/export-json', ['controller' => 'BechlemIdentifiers', 'action' => 'exportJson']);

            /*
             * Bechlem Printers Controller
             */
            $routes
                ->connect('/bechlem-printers', ['controller' => 'BechlemPrinters', 'action' => 'index']);
            $routes
                ->connect('/bechlem-printer/{id}', ['controller' => 'BechlemPrinters', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-printers/update-all', ['controller' => 'BechlemPrinters', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-printers/export-xlsx', ['controller' => 'BechlemPrinters', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-printers/export-csv', ['controller' => 'BechlemPrinters', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-printers/export-xml', ['controller' => 'BechlemPrinters', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-printers/export-json', ['controller' => 'BechlemPrinters', 'action' => 'exportJson']);

            /*
             * Bechlem Printer Serieses Controller
             */
            $routes
                ->connect('/bechlem-printer-serieses', ['controller' => 'BechlemPrinterSerieses', 'action' => 'index']);
            $routes
                ->connect('/bechlem-printer-series/{id}', ['controller' => 'BechlemPrinterSerieses', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-printer-serieses/update-all', ['controller' => 'BechlemPrinterSerieses', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-printer-serieses/export-xlsx', ['controller' => 'BechlemPrinterSerieses', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-printer-serieses/export-csv', ['controller' => 'BechlemPrinterSerieses', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-printer-serieses/export-xml', ['controller' => 'BechlemPrinterSerieses', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-printer-serieses/export-json', ['controller' => 'BechlemPrinterSerieses', 'action' => 'exportJson']);

            /*
             * Bechlem Printer To Supplies Controller
             */
            $routes
                ->connect('/bechlem-printer-to-supplies', ['controller' => 'BechlemPrinterToSupplies', 'action' => 'index']);
            $routes
                ->connect('/bechlem-printer-to-supply/{id}', ['controller' => 'BechlemPrinterToSupplies', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-printer-to-supplies/update-all', ['controller' => 'BechlemPrinterToSupplies', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-printer-to-supplies/export-xlsx', ['controller' => 'BechlemPrinterToSupplies', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-printer-to-supplies/export-csv', ['controller' => 'BechlemPrinterToSupplies', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-printer-to-supplies/export-xml', ['controller' => 'BechlemPrinterToSupplies', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-printer-to-supplies/export-json', ['controller' => 'BechlemPrinterToSupplies', 'action' => 'exportJson']);

            /*
             * Bechlem Resellers Controller
             */
            $routes
                ->connect('/bechlem-resellers', ['controller' => 'BechlemResellers', 'action' => 'index']);
            $routes
                ->connect('/bechlem-reseller/{id}', ['controller' => 'BechlemResellers', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-resellers/update-all', ['controller' => 'BechlemResellers', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-resellers/export-xlsx', ['controller' => 'BechlemResellers', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-resellers/export-csv', ['controller' => 'BechlemResellers', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-resellers/export-xml', ['controller' => 'BechlemResellers', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-resellers/export-json', ['controller' => 'BechlemResellers', 'action' => 'exportJson']);

            /*
             * Bechlem Reseller Items Controller
             */
            $routes
                ->connect('/bechlem-reseller-items', ['controller' => 'BechlemResellerItems', 'action' => 'index']);
            $routes
                ->connect('/bechlem-reseller-item/{id}', ['controller' => 'BechlemResellerItems', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-reseller-items/update-all', ['controller' => 'BechlemResellerItems', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-reseller-items/export-xlsx', ['controller' => 'BechlemResellerItems', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-reseller-items/export-csv', ['controller' => 'BechlemResellerItems', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-reseller-items/export-xml', ['controller' => 'BechlemResellerItems', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-reseller-items/export-json', ['controller' => 'BechlemResellerItems', 'action' => 'exportJson']);

            /*
             * Bechlem Supplies Controller
             */
            $routes
                ->connect('/bechlem-supplies', ['controller' => 'BechlemSupplies', 'action' => 'index']);
            $routes
                ->connect('/bechlem-supply/{id}', ['controller' => 'BechlemSupplies', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-supplies/update-all', ['controller' => 'BechlemSupplies', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-supplies/export-xlsx', ['controller' => 'BechlemSupplies', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-supplies/export-csv', ['controller' => 'BechlemSupplies', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-supplies/export-xml', ['controller' => 'BechlemSupplies', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-supplies/export-json', ['controller' => 'BechlemSupplies', 'action' => 'exportJson']);

            /*
             * Bechlem Supply Serieses Controller
             */
            $routes
                ->connect('/bechlem-supply-serieses', ['controller' => 'BechlemSupplySerieses', 'action' => 'index']);
            $routes
                ->connect('/bechlem-supply-series/{id}', ['controller' => 'BechlemSupplySerieses', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-supply-serieses/update-all', ['controller' => 'BechlemSupplySerieses', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-supply-serieses/export-xlsx', ['controller' => 'BechlemSupplySerieses', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-supply-serieses/export-csv', ['controller' => 'BechlemSupplySerieses', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-supply-serieses/export-xml', ['controller' => 'BechlemSupplySerieses', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-supply-serieses/export-json', ['controller' => 'BechlemSupplySerieses', 'action' => 'exportJson']);

            /*
             * Bechlem Supply To Oem References Controller
             */
            $routes
                ->connect('/bechlem-supply-to-oem-references', ['controller' => 'BechlemSupplyToOemReferences', 'action' => 'index']);
            $routes
                ->connect('/bechlem-supply-to-oem-reference/{id}', ['controller' => 'BechlemSupplyToOemReferences', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-supply-to-oem-references/update-all', ['controller' => 'BechlemSupplyToOemReferences', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-supply-to-oem-references/export-xlsx', ['controller' => 'BechlemSupplyToOemReferences', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-supply-to-oem-references/export-csv', ['controller' => 'BechlemSupplyToOemReferences', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-supply-to-oem-references/export-xml', ['controller' => 'BechlemSupplyToOemReferences', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-supply-to-oem-references/export-json', ['controller' => 'BechlemSupplyToOemReferences', 'action' => 'exportJson']);

            /*
             * Bechlem Supply To Supplies Controller
             */
            $routes
                ->connect('/bechlem-supply-to-supplies', ['controller' => 'BechlemSupplyToSupplies', 'action' => 'index']);
            $routes
                ->connect('/bechlem-supply-to-supply/{id}', ['controller' => 'BechlemSupplyToSupplies', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/bechlem-supply-to-supplies/update-all', ['controller' => 'BechlemSupplyToSupplies', 'action' => 'updateAll']);
            $routes
                ->connect('/bechlem-supply-to-supplies/export-xlsx', ['controller' => 'BechlemSupplyToSupplies', 'action' => 'exportXlsx']);
            $routes
                ->connect('/bechlem-supply-to-supplies/export-csv', ['controller' => 'BechlemSupplyToSupplies', 'action' => 'exportCsv']);
            $routes
                ->connect('/bechlem-supply-to-supplies/export-xml', ['controller' => 'BechlemSupplyToSupplies', 'action' => 'exportXml']);
            $routes
                ->connect('/bechlem-supply-to-supplies/export-json', ['controller' => 'BechlemSupplyToSupplies', 'action' => 'exportJson']);

            /*
             * Countries Controller
             */
            $routes
                ->connect('/countries', ['controller' => 'Countries', 'action' => 'index']);
            $routes
                ->connect('/country/{id}', ['controller' => 'Countries', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/country/add', ['controller' => 'Countries', 'action' => 'add']);
            $routes
                ->connect('/country/edit/{id}', ['controller' => 'Countries', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/country/delete/{id}', ['controller' => 'Countries', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/countries/import', ['controller' => 'Countries', 'action' => 'import']);
            $routes
                ->connect('/countries/export-xlsx', ['controller' => 'Countries', 'action' => 'exportXlsx']);
            $routes
                ->connect('/countries/export-csv', ['controller' => 'Countries', 'action' => 'exportCsv']);
            $routes
                ->connect('/countries/export-xml', ['controller' => 'Countries', 'action' => 'exportXml']);
            $routes
                ->connect('/countries/export-json', ['controller' => 'Countries', 'action' => 'exportJson']);

            /*
             * Dashboards Controller
             */
            $routes
                ->connect('/dashboard', ['controller' => 'Dashboards', 'action' => 'dashboard']);
            $routes
                ->connect('/application-documentation', ['controller' => 'Dashboards', 'action' => 'applicationDocumentation']);
            $routes
                ->connect('/application-documentation-pdf', ['controller' => 'Dashboards', 'action' => 'applicationDocumentationPdf', '_ext' => 'pdf']);
            $routes
                ->connect('/mit-license-documentation', ['controller' => 'Dashboards', 'action' => 'mitLicenseDocumentation']);
            $routes
                ->connect('/mit-license-documentation-pdf', ['controller' => 'Dashboards', 'action' => 'mitLicenseDocumentationPdf', '_ext' => 'pdf']);
            $routes
                ->connect('/rest-api-documentation', ['controller' => 'Dashboards', 'action' => 'restApiDocumentation']);
            $routes
                ->connect('/rest-api-documentation-pdf', ['controller' => 'Dashboards', 'action' => 'restApiDocumentationPdf', '_ext' => 'pdf']);

            /*
             * Domains Controller
             */
            $routes
                ->connect('/domains', ['controller' => 'Domains', 'action' => 'index']);
            $routes
                ->connect('/domain/{id}', ['controller' => 'Domains', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/domain/add', ['controller' => 'Domains', 'action' => 'add']);
            $routes
                ->connect('/domain/edit/{id}', ['controller' => 'Domains', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/domain/delete/{id}', ['controller' => 'Domains', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/domains/import', ['controller' => 'Domains', 'action' => 'import']);
            $routes
                ->connect('/domains/export-xlsx', ['controller' => 'Domains', 'action' => 'exportXlsx']);
            $routes
                ->connect('/domains/export-csv', ['controller' => 'Domains', 'action' => 'exportCsv']);
            $routes
                ->connect('/domains/export-xml', ['controller' => 'Domains', 'action' => 'exportXml']);
            $routes
                ->connect('/domains/export-json', ['controller' => 'Domains', 'action' => 'exportJson']);

            /*
             * Locales Controller
             */
            $routes
                ->connect('/locales', ['controller' => 'Locales', 'action' => 'index']);
            $routes
                ->connect('/locale/{id}', ['controller' => 'Locales', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/locale/add', ['controller' => 'Locales', 'action' => 'add']);
            $routes
                ->connect('/locale/edit/{id}', ['controller' => 'Locales', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes->connect('/locale/delete/{id}', ['controller' => 'Locales', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/locales/import', ['controller' => 'Locales', 'action' => 'import']);
            $routes
                ->connect('/locales/export-xlsx', ['controller' => 'Locales', 'action' => 'exportXlsx']);
            $routes
                ->connect('/locales/export-csv', ['controller' => 'Locales', 'action' => 'exportCsv']);
            $routes
                ->connect('/locales/export-xml', ['controller' => 'Locales', 'action' => 'exportXml']);
            $routes
                ->connect('/locales/export-json', ['controller' => 'Locales', 'action' => 'exportJson']);

            /*
             * Logs Controller
             */
            $routes
                ->connect('/logs', ['controller' => 'Logs', 'action' => 'index']);
            $routes
                ->connect('/log/{id}', ['controller' => 'Logs', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/log/add', ['controller' => 'Logs', 'action' => 'add']);
            $routes
                ->connect('/log/edit/{id}', ['controller' => 'Logs', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/log/delete/{id}', ['controller' => 'Logs', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/logs/export-xlsx', ['controller' => 'Logs', 'action' => 'exportXlsx']);
            $routes
                ->connect('/logs/export-csv', ['controller' => 'Logs', 'action' => 'exportCsv']);
            $routes
                ->connect('/logs/export-xml', ['controller' => 'Logs', 'action' => 'exportXml']);
            $routes
                ->connect('/logs/export-json', ['controller' => 'Logs', 'action' => 'exportJson']);

            /*
             * Products Controller
             */
            $routes
                ->connect('/products', ['controller' => 'Products', 'action' => 'index']);
            $routes
                ->connect('/product/{id}', ['controller' => 'Products', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product/add/{productTypeAlias}', ['controller' => 'Products', 'action' => 'add'])
                ->setPass(['productTypeAlias']);
            $routes
                ->connect('/product/edit/{id}', ['controller' => 'Products', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product/copy/{id}', ['controller' => 'Products', 'action' => 'copy'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product/delete/{id}', ['controller' => 'Products', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/products/export-xlsx', ['controller' => 'Products', 'action' => 'exportXlsx']);
            $routes
                ->connect('/products/export-csv', ['controller' => 'Products', 'action' => 'exportCsv']);
            $routes
                ->connect('/products/export-xml', ['controller' => 'Products', 'action' => 'exportXml']);
            $routes
                ->connect('/products/export-json', ['controller' => 'Products', 'action' => 'exportJson']);

            /*
             * Product Brands Controller
             */
            $routes
                ->connect('/product-brands', ['controller' => 'ProductBrands', 'action' => 'index']);
            $routes
                ->connect('/product-brand/{id}', ['controller' => 'ProductBrands', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-brand/add', ['controller' => 'ProductBrands', 'action' => 'add']);
            $routes
                ->connect('/product-brand/edit/{id}', ['controller' => 'ProductBrands', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-brand/delete/{id}', ['controller' => 'ProductBrands', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-brands/import', ['controller' => 'ProductBrands', 'action' => 'import']);
            $routes
                ->connect('/product-brands/export-xlsx', ['controller' => 'ProductBrands', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-brands/export-csv', ['controller' => 'ProductBrands', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-brands/export-xml', ['controller' => 'ProductBrands', 'action' => 'exportXml']);
            $routes
                ->connect('/product-brands/export-json', ['controller' => 'ProductBrands', 'action' => 'exportJson']);

            /*
             * Product Categories Controller
             */
            $routes
                ->connect('/product-categories', ['controller' => 'ProductCategories', 'action' => 'index']);
            $routes
                ->connect('/product-category/ajax-move', ['controller' => 'ProductCategories', 'action' => 'ajaxMove']);
            $routes
                ->connect('/product-category/move-up/{id}', ['controller' => 'ProductCategories', 'action' => 'moveUp'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-category/move-down/{id}', ['controller' => 'ProductCategories', 'action' => 'moveDown'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-category/{id}', ['controller' => 'ProductCategories', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-category/add', ['controller' => 'ProductCategories', 'action' => 'add']);
            $routes
                ->connect('/product-category/edit/{id}', ['controller' => 'ProductCategories', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-category/delete/{id}', ['controller' => 'ProductCategories', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-categories/import', ['controller' => 'ProductCategories', 'action' => 'import']);
            $routes
                ->connect('/product-categories/export-xlsx', ['controller' => 'ProductCategories', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-categories/export-csv', ['controller' => 'ProductCategories', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-categories/export-xml', ['controller' => 'ProductCategories', 'action' => 'exportXml']);
            $routes
                ->connect('/product-categories/export-json', ['controller' => 'ProductCategories', 'action' => 'exportJson']);

            /*
             * Product Conditions Controller
             */
            $routes
                ->connect('/product-conditions', ['controller' => 'ProductConditions', 'action' => 'index']);
            $routes
                ->connect('/product-condition/{id}', ['controller' => 'ProductConditions', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-condition/add', ['controller' => 'ProductConditions', 'action' => 'add']);
            $routes
                ->connect('/product-condition/edit/{id}', ['controller' => 'ProductConditions', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-condition/delete/{id}', ['controller' => 'ProductConditions', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-conditions/import', ['controller' => 'ProductConditions', 'action' => 'import']);
            $routes
                ->connect('/product-conditions/export-xlsx', ['controller' => 'ProductConditions', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-conditions/export-csv', ['controller' => 'ProductConditions', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-conditions/export-xml', ['controller' => 'ProductConditions', 'action' => 'exportXml']);
            $routes
                ->connect('/product-conditions/export-json', ['controller' => 'ProductConditions', 'action' => 'exportJson']);

            /*
             * Product Delivery Times Controller
             */
            $routes
                ->connect('/product-delivery-times', ['controller' => 'ProductDeliveryTimes', 'action' => 'index']);
            $routes
                ->connect('/product-delivery-time/{id}', ['controller' => 'ProductDeliveryTimes', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-delivery-time/add', ['controller' => 'ProductDeliveryTimes', 'action' => 'add']);
            $routes
                ->connect('/product-delivery-time/edit/{id}', ['controller' => 'ProductDeliveryTimes', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-delivery-time/delete/{id}', ['controller' => 'ProductDeliveryTimes', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-delivery-times/import', ['controller' => 'ProductDeliveryTimes', 'action' => 'import']);
            $routes
                ->connect('/product-delivery-times/export-xlsx', ['controller' => 'ProductDeliveryTimes', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-delivery-times/export-csv', ['controller' => 'ProductDeliveryTimes', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-delivery-times/export-xml', ['controller' => 'ProductDeliveryTimes', 'action' => 'exportXml']);
            $routes
                ->connect('/product-delivery-times/export-json', ['controller' => 'ProductDeliveryTimes', 'action' => 'exportJson']);

            /*
             * Product Intrastat Codes Controller
             */
            $routes
                ->connect('/product-intrastat-codes', ['controller' => 'ProductIntrastatCodes', 'action' => 'index']);
            $routes
                ->connect('/product-intrastat-code/{id}', ['controller' => 'ProductIntrastatCodes', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-intrastat-code/add', ['controller' => 'ProductIntrastatCodes', 'action' => 'add']);
            $routes
                ->connect('/product-intrastat-code/edit/{id}', ['controller' => 'ProductIntrastatCodes', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-intrastat-code/delete/{id}', ['controller' => 'ProductIntrastatCodes', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-intrastat-codes/import', ['controller' => 'ProductIntrastatCodes', 'action' => 'import']);
            $routes
                ->connect('/product-intrastat-codes/export-xlsx', ['controller' => 'ProductIntrastatCodes', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-intrastat-codes/export-csv', ['controller' => 'ProductIntrastatCodes', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-intrastat-codes/export-xml', ['controller' => 'ProductIntrastatCodes', 'action' => 'exportXml']);
            $routes
                ->connect('/product-intrastat-codes/export-json', ['controller' => 'ProductIntrastatCodes', 'action' => 'exportJson']);

            /*
             * Product Manufacturers Controller
             */
            $routes
                ->connect('/product-manufacturers', ['controller' => 'ProductManufacturers', 'action' => 'index']);
            $routes
                ->connect('/product-manufacturers/update-all', ['controller' => 'ProductManufacturers', 'action' => 'updateAll']);
            $routes
                ->connect('/product-manufacturer/{id}', ['controller' => 'ProductManufacturers', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-manufacturer/add', ['controller' => 'ProductManufacturers', 'action' => 'add']);
            $routes
                ->connect('/product-manufacturer/edit/{id}', ['controller' => 'ProductManufacturers', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-manufacturer/delete/{id}', ['controller' => 'ProductManufacturers', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-manufacturers/import', ['controller' => 'ProductManufacturers', 'action' => 'import']);
            $routes
                ->connect('/product-manufacturers/export-xlsx', ['controller' => 'ProductManufacturers', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-manufacturers/export-csv', ['controller' => 'ProductManufacturers', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-manufacturers/export-xml', ['controller' => 'ProductManufacturers', 'action' => 'exportXml']);
            $routes
                ->connect('/product-manufacturers/export-json', ['controller' => 'ProductManufacturers', 'action' => 'exportJson']);

            /*
             * Product Suppliers Controller
             */
            $routes
                ->connect('/product-suppliers', ['controller' => 'ProductSuppliers', 'action' => 'index']);
            $routes
                ->connect('/product-supplier/{id}', ['controller' => 'ProductSuppliers', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-supplier/add', ['controller' => 'ProductSuppliers', 'action' => 'add']);
            $routes
                ->connect('/product-supplier/edit/{id}', ['controller' => 'ProductSuppliers', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-supplier/delete/{id}', ['controller' => 'ProductSuppliers', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-suppliers/import', ['controller' => 'ProductSuppliers', 'action' => 'import']);
            $routes
                ->connect('/product-suppliers/export-xlsx', ['controller' => 'ProductSuppliers', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-suppliers/export-csv', ['controller' => 'ProductSuppliers', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-suppliers/export-xml', ['controller' => 'ProductSuppliers', 'action' => 'exportXml']);
            $routes
                ->connect('/product-suppliers/export-json', ['controller' => 'ProductSuppliers', 'action' => 'exportJson']);

            /*
             * Product Tax Classes Controller
             */
            $routes
                ->connect('/product-tax-classes', ['controller' => 'ProductTaxClasses', 'action' => 'index']);
            $routes
                ->connect('/product-tax-class/{id}', ['controller' => 'ProductTaxClasses', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-tax-class/add', ['controller' => 'ProductTaxClasses', 'action' => 'add']);
            $routes
                ->connect('/product-tax-class/edit/{id}', ['controller' => 'ProductTaxClasses', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-tax-class/delete/{id}', ['controller' => 'ProductTaxClasses', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-tax-classes/import', ['controller' => 'ProductTaxClasses', 'action' => 'import']);
            $routes
                ->connect('/product-tax-classes/export-xlsx', ['controller' => 'ProductTaxClasses', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-tax-classes/export-csv', ['controller' => 'ProductTaxClasses', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-tax-classes/export-xml', ['controller' => 'ProductTaxClasses', 'action' => 'exportXml']);
            $routes
                ->connect('/product-tax-classes/export-json', ['controller' => 'ProductTaxClasses', 'action' => 'exportJson']);

            /*
             * Product Types Controller
             */
            $routes
                ->connect('/product-types', ['controller' => 'ProductTypes', 'action' => 'index']);
            $routes
                ->connect('/product-type/{id}', ['controller' => 'ProductTypes', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-type/add', ['controller' => 'ProductTypes', 'action' => 'add']);
            $routes
                ->connect('/product-type/edit/{id}', ['controller' => 'ProductTypes', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-type/delete/{id}', ['controller' => 'ProductTypes', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-types/import', ['controller' => 'ProductTypes', 'action' => 'import']);
            $routes
                ->connect('/product-types/export-xlsx', ['controller' => 'ProductTypes', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-types/export-csv', ['controller' => 'ProductTypes', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-types/export-xml', ['controller' => 'ProductTypes', 'action' => 'exportXml']);
            $routes
                ->connect('/product-types/export-json', ['controller' => 'ProductTypes', 'action' => 'exportJson']);

            /*
             * Product Type Attributes Controller
             */
            $routes
                ->connect('/product-type-attributes', ['controller' => 'ProductTypeAttributes', 'action' => 'index']);
            $routes
                ->connect('/product-type-attribute/{id}', ['controller' => 'ProductTypeAttributes', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-type-attribute/add', ['controller' => 'ProductTypeAttributes', 'action' => 'add']);
            $routes
                ->connect('/product-type-attribute/edit/{id}', ['controller' => 'ProductTypeAttributes', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-type-attribute/delete/{id}', ['controller' => 'ProductTypeAttributes', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-type-attributes/import', ['controller' => 'ProductTypeAttributes', 'action' => 'import']);
            $routes
                ->connect('/product-type-attributes/export-xlsx', ['controller' => 'ProductTypeAttributes', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-type-attributes/export-csv', ['controller' => 'ProductTypeAttributes', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-type-attributes/export-xml', ['controller' => 'ProductTypeAttributes', 'action' => 'exportXml']);
            $routes
                ->connect('/product-type-attributes/export-json', ['controller' => 'ProductTypeAttributes', 'action' => 'exportJson']);

            /*
             * Product Type Attribute Choices Controller
             */
            $routes
                ->connect('/product-type-attribute-choices', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'index']);
            $routes
                ->connect('/product-type-attribute-choice/{id}', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-type-attribute-choice/add', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'add']);
            $routes
                ->connect('/product-type-attribute-choice/edit/{id}', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-type-attribute-choice/delete/{id}', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-type-attribute-choices/import', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'import']);
            $routes
                ->connect('/product-type-attribute-choices/export-xlsx', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-type-attribute-choices/export-csv', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-type-attribute-choices/export-xml', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'exportXml']);
            $routes
                ->connect('/product-type-attribute-choices/export-json', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'exportJson']);

            /*
             * Product Product Type Attribute Values Controller
             */
            $routes
                ->connect('/product-product-type-attribute-values', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'index']);
            $routes
                ->connect('/product-product-type-attribute-value/{id}', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-product-type-attribute-value/add', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'add']);
            $routes
                ->connect('/product-product-type-attribute-value/edit/{id}', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-product-type-attribute-value/delete/{id}', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/product-product-type-attribute-values/import', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'import']);
            $routes
                ->connect('/product-product-type-attribute-values/export-xlsx', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'exportXlsx']);
            $routes
                ->connect('/product-product-type-attribute-values/export-csv', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'exportCsv']);
            $routes
                ->connect('/product-product-type-attribute-values/export-xml', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'exportXml']);
            $routes
                ->connect('/product-product-type-attribute-values/export-json', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'exportJson']);

            /*
             * Roles Controller
             */
            $routes
                ->connect('/roles', ['controller' => 'Roles', 'action' => 'index']);
            $routes
                ->connect('/role/{id}', ['controller' => 'Roles', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/role/add', ['controller' => 'Roles', 'action' => 'add']);
            $routes
                ->connect('/role/edit/{id}', ['controller' => 'Roles', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/role/delete/{id}', ['controller' => 'Roles', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/roles/export-xlsx', ['controller' => 'Roles', 'action' => 'exportXlsx']);
            $routes
                ->connect('/roles/export-csv', ['controller' => 'Roles', 'action' => 'exportCsv']);
            $routes
                ->connect('/roles/export-xml', ['controller' => 'Roles', 'action' => 'exportXml']);
            $routes
                ->connect('/roles/export-json', ['controller' => 'Roles', 'action' => 'exportJson']);

            /*
             * Settings Controller
             */
            $routes
                ->connect('/settings', ['controller' => 'Settings', 'action' => 'index']);
            $routes
                ->connect('/setting/{id}', ['controller' => 'Settings', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/setting/add', ['controller' => 'Settings', 'action' => 'add']);
            $routes
                ->connect('/setting/edit/{id}', ['controller' => 'Settings', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/setting/delete/{id}', ['controller' => 'Settings', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/settings/import', ['controller' => 'Settings', 'action' => 'import']);
            $routes
                ->connect('/settings/export-xlsx', ['controller' => 'Settings', 'action' => 'exportXlsx']);
            $routes
                ->connect('/settings/export-csv', ['controller' => 'Settings', 'action' => 'exportCsv']);
            $routes
                ->connect('/settings/export-xml', ['controller' => 'Settings', 'action' => 'exportXml']);
            $routes
                ->connect('/settings/export-json', ['controller' => 'Settings', 'action' => 'exportJson']);

            /*
             * Users Controller
             */
            $routes
                ->connect('/user/login', ['controller' => 'Users', 'action' => 'login']);
            $routes
                ->connect('/user/logout', ['controller' => 'Users', 'action' => 'logout']);
            $routes
                ->connect('/user/forgot', ['controller' => 'Users', 'action' => 'forgot']);
            $routes
                ->connect('/user/reset/{username}/{token}', ['controller' => 'Users', 'action' => 'reset'])
                ->setPass(['username', 'token'])
                ->setPatterns(['token' => '[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{12}']);
            $routes
                ->connect('/users', ['controller' => 'Users', 'action' => 'index']);
            $routes
                ->connect('/user/{id}', ['controller' => 'Users', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/profile/{id}', ['controller' => 'Users', 'action' => 'profile'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/user/add', ['controller' => 'Users', 'action' => 'add']);
            $routes
                ->connect('/user/edit/{id}', ['controller' => 'Users', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/user/reset-password/{id}', ['controller' => 'Users', 'action' => 'resetPassword'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/user/delete/{id}', ['controller' => 'Users', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/users/export-xlsx', ['controller' => 'Users', 'action' => 'exportXlsx']);
            $routes
                ->connect('/users/export-csv', ['controller' => 'Users', 'action' => 'exportCsv']);
            $routes
                ->connect('/users/export-xml', ['controller' => 'Users', 'action' => 'exportXml']);
            $routes
                ->connect('/users/export-json', ['controller' => 'Users', 'action' => 'exportJson']);

            /*
             * UserProfiles Controller
             */
            $routes
                ->connect('/user-profiles', ['controller' => 'UserProfiles', 'action' => 'index']);
            $routes
                ->connect('/user-profile/{id}', ['controller' => 'UserProfiles', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/user-profile/add', ['controller' => 'UserProfiles', 'action' => 'add']);
            $routes
                ->connect('/user-profile/edit/{id}', ['controller' => 'UserProfiles', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/user-profile/delete/{id}', ['controller' => 'UserProfiles', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/user-profiles/export-xlsx', ['controller' => 'UserProfiles', 'action' => 'exportXlsx']);
            $routes
                ->connect('/user-profiles/export-csv', ['controller' => 'UserProfiles', 'action' => 'exportCsv']);
            $routes
                ->connect('/user-profiles/export-xml', ['controller' => 'UserProfiles', 'action' => 'exportXml']);
            $routes
                ->connect('/user-profiles/export-json', ['controller' => 'UserProfiles', 'action' => 'exportJson']);

            /*
             * Registrations Controller
             */
            $routes
                ->connect('/registrations', ['controller' => 'Registrations', 'action' => 'index']);
            $routes
                ->connect('/registration/{id}', ['controller' => 'Registrations', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/registration/add', ['controller' => 'Registrations', 'action' => 'add']);
            $routes
                ->connect('/registration/edit/{id}', ['controller' => 'Registrations', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/registration/delete/{id}', ['controller' => 'Registrations', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/registrations/import', ['controller' => 'Registrations', 'action' => 'import']);
            $routes
                ->connect('/registrations/export-xlsx', ['controller' => 'Registrations', 'action' => 'exportXlsx']);
            $routes
                ->connect('/registrations/export-csv', ['controller' => 'Registrations', 'action' => 'exportCsv']);
            $routes
                ->connect('/registrations/export-xml', ['controller' => 'Registrations', 'action' => 'exportXml']);
            $routes
                ->connect('/registrations/export-json', ['controller' => 'Registrations', 'action' => 'exportJson']);

            /*
             * Registration Types Controller
             */
            $routes
                ->connect('/registration-types', ['controller' => 'RegistrationTypes', 'action' => 'index']);
            $routes
                ->connect('/registration-type/{id}', ['controller' => 'RegistrationTypes', 'action' => 'view'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/registration-type/add', ['controller' => 'RegistrationTypes', 'action' => 'add']);
            $routes
                ->connect('/registration-type/edit/{id}', ['controller' => 'RegistrationTypes', 'action' => 'edit'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/registration-type/copy/{id}', ['controller' => 'RegistrationTypes', 'action' => 'copy'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/registration-type/delete/{id}', ['controller' => 'RegistrationTypes', 'action' => 'delete'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);
            $routes
                ->connect('/registration-types/import', ['controller' => 'RegistrationTypes', 'action' => 'import']);
            $routes
                ->connect('/registration-types/export-xlsx', ['controller' => 'RegistrationTypes', 'action' => 'exportXlsx']);
            $routes
                ->connect('/registration-types/export-csv', ['controller' => 'RegistrationTypes', 'action' => 'exportCsv']);
            $routes
                ->connect('/registration-types/export-xml', ['controller' => 'RegistrationTypes', 'action' => 'exportXml']);
            $routes
                ->connect('/registration-types/export-json', ['controller' => 'RegistrationTypes', 'action' => 'exportJson']);

        });

        /*
        * Api Prefix Routing
        */
        $routes->prefix('Api', ['_namePrefix' => 'api:'], function (RouteBuilder $builder) {

            $builder->registerMiddleware('auth', new \Authentication\Middleware\AuthenticationMiddleware($this));
            $builder->applyMiddleware('auth');

            // Parse specified extensions from URLs
            $builder->setExtensions(['json', 'xml']);

            /*
             * BechlemConnectConfigs Controller
             */
            $builder
                ->connect('/bechlem-connect-configs', ['controller' => 'BechlemConnectConfigs', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-connect-configs/{id}', ['controller' => 'BechlemConnectConfigs', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemConnectRequests Controller
             */
            $builder
                ->connect('/bechlem-connect-requests', ['controller' => 'BechlemConnectRequests', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-connect-requests/{id}', ['controller' => 'BechlemConnectRequests', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemBrands Controller
             */
            $builder
                ->connect('/bechlem-brands', ['controller' => 'BechlemBrands', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-brands/{id}', ['controller' => 'BechlemBrands', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemCategories Controller
             */
            $builder
                ->connect('/bechlem-categories', ['controller' => 'BechlemCategories', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-categories/{id}', ['controller' => 'BechlemCategories', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemIdentifiers Controller
             */
            $builder
                ->connect('/bechlem-identifiers', ['controller' => 'BechlemIdentifiers', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-identifiers/{id}', ['controller' => 'BechlemIdentifiers', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemPrinters Controller
             */
            $builder
                ->connect('/bechlem-printers', ['controller' => 'BechlemPrinters', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-printers/{id}', ['controller' => 'BechlemPrinters', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemPrinterSerieses Controller
             */
            $builder
                ->connect('/bechlem-printer-serieses', ['controller' => 'BechlemPrinterSerieses', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-printer-serieses/{id}', ['controller' => 'BechlemPrinterSerieses', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemPrinterToSupplies Controller
             */
            $builder
                ->connect('/bechlem-printer-to-supplies', ['controller' => 'BechlemPrinterToSupplies', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-printer-to-supplies/{id}', ['controller' => 'BechlemPrinterToSupplies', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemProducts Controller
             */
            $builder
                ->connect('/bechlem-products', ['controller' => 'BechlemProducts', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-products/{id}', ['controller' => 'BechlemProducts', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemProductAccessories Controller
             */
            $builder
                ->connect('/bechlem-product-accessories', ['controller' => 'BechlemProductAccessories', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-product-accessories/{id}', ['controller' => 'BechlemProductAccessories', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemResellers Controller
             */
            $builder
                ->connect('/bechlem-resellers', ['controller' => 'BechlemResellers', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-resellers/{id}', ['controller' => 'BechlemResellers', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemResellerItems Controller
             */
            $builder
                ->connect('/bechlem-reseller-items', ['controller' => 'BechlemResellerItems', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-reseller-items/{id}', ['controller' => 'BechlemResellerItems', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemSupplies Controller
             */
            $builder
                ->connect('/bechlem-supplies', ['controller' => 'BechlemSupplies', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-supplies/{id}', ['controller' => 'BechlemSupplies', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemSupplySerieses Controller
             */
            $builder
                ->connect('/bechlem-supply-serieses', ['controller' => 'BechlemSupplySerieses', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-supply-serieses/{id}', ['controller' => 'BechlemSupplySerieses', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemSupplyToOemReferences Controller
             */
            $builder
                ->connect('/bechlem-supply-to-oem-references', ['controller' => 'BechlemSupplyToOemReferences', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-supply-to-oem-references/{id}', ['controller' => 'BechlemSupplyToOemReferences', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * BechlemSupplyToSupplies Controller
             */
            $builder
                ->connect('/bechlem-supply-to-supplies', ['controller' => 'BechlemSupplyToSupplies', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/bechlem-supply-to-supplies/{id}', ['controller' => 'BechlemSupplyToSupplies', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * Countries Controller
             */
            $builder
                ->connect('/countries', ['controller' => 'Countries', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/countries/{id}', ['controller' => 'Countries', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * Domains Controller
             */
            $builder
                ->connect('/domains', ['controller' => 'Domains', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/domains/{id}', ['controller' => 'Domains', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * Locales Controller
             */
            $builder
                ->connect('/locales', ['controller' => 'Locales', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/locales/{id}', ['controller' => 'Locales', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * Logs Controller
             */
            $builder
                ->connect('/logs', ['controller' => 'Logs', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/logs/{id}', ['controller' => 'Logs', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductBrands Controller
             */
            $builder
                ->connect('/product-brands', ['controller' => 'ProductBrands', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-brands/{id}', ['controller' => 'ProductBrands', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductCategories Controller
             */
            $builder
                ->connect('/product-categories', ['controller' => 'ProductCategories', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-categories/{id}', ['controller' => 'ProductCategories', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductConditions Controller
             */
            $builder
                ->connect('/product-conditions', ['controller' => 'ProductConditions', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-conditions/{id}', ['controller' => 'ProductConditions', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductDeliveryTimes Controller
             */
            $builder
                ->connect('/product-delivery-times', ['controller' => 'ProductDeliveryTimes', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-delivery-times/{id}', ['controller' => 'ProductDeliveryTimes', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductIntrastatCodes Controller
             */
            $builder
                ->connect('/product-intrastat-codes', ['controller' => 'ProductIntrastatCodes', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-intrastat-codes/{id}', ['controller' => 'ProductIntrastatCodes', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductManufacturers Controller
             */
            $builder
                ->connect('/product-manufacturers', ['controller' => 'ProductManufacturers', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-manufacturers/{id}', ['controller' => 'ProductManufacturers', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductProductTypeAttributeValues Controller
             */
            $builder
                ->connect('/product-product-type-attribute-values', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-product-type-attribute-values/{id}', ['controller' => 'ProductProductTypeAttributeValues', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * Products Controller
             */
            $builder
                ->connect('/products', ['controller' => 'Products', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/products/{id}', ['controller' => 'Products', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductSuppliers Controller
             */
            $builder
                ->connect('/product-suppliers', ['controller' => 'ProductSuppliers', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-suppliers/{id}', ['controller' => 'ProductSuppliers', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductTaxClasses Controller
             */
            $builder
                ->connect('/product-tax-classes', ['controller' => 'ProductTaxClasses', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-tax-classes/{id}', ['controller' => 'ProductTaxClasses', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductTypeAttributeChoices Controller
             */
            $builder
                ->connect('/product-type-attribute-choices', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-type-attribute-choices/{id}', ['controller' => 'ProductTypeAttributeChoices', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductTypeAttributes Controller
             */
            $builder
                ->connect('/product-type-attributes', ['controller' => 'ProductTypeAttributes', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-type-attributes/{id}', ['controller' => 'ProductTypeAttributes', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * ProductTypes Controller
             */
            $builder
                ->connect('/product-types', ['controller' => 'ProductTypes', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/product-types/{id}', ['controller' => 'ProductTypes', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * Registrations Controller
             */
            $builder
                ->connect('/registrations', ['controller' => 'Registrations', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/registrations/{id}', ['controller' => 'Registrations', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * RegistrationTypes Controller
             */
            $builder
                ->connect('/registration-types', ['controller' => 'RegistrationTypes', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/registration-types/{id}', ['controller' => 'RegistrationTypes', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * Roles Controller
             */
            $builder
                ->connect('/roles', ['controller' => 'Roles', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/roles/{id}', ['controller' => 'Roles', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * Settings Controller
             */
            $builder
                ->connect('/settings', ['controller' => 'Settings', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/settings/{id}', ['controller' => 'Settings', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
            * UserProfiles Controller
            */
            $builder
                ->connect('/user-profiles', ['controller' => 'UserProfiles', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/user-profiles/{id}', ['controller' => 'UserProfiles', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

            /*
             * Users Controller
             */
            $builder
                ->connect('/oauth/token', ['controller' => 'Users', 'action' => 'token', '_ext' => 'json', '_method' => 'POST']);
            $builder
                ->connect('/users', ['controller' => 'Users', 'action' => 'index', '_ext' => 'json', '_method' => 'GET']);
            $builder
                ->connect('/users/{id}', ['controller' => 'Users', 'action' => 'view', '_ext' => 'json', '_method' => 'GET'])
                ->setPass(['id'])
                ->setPatterns(['id' => '\d+']);

        });
    }
);
