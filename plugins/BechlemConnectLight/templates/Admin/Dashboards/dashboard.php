<?php

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
use Cake\Core\Configure;

// Get session object
$session = $this->getRequest()->getSession();

$backendButtonColor = 'light';
if (Configure::check('BechlemConnectLight.settings.backendButtonColor')):
    $backendButtonColor = Configure::read('BechlemConnectLight.settings.backendButtonColor');
endif;

$backendBoxColor = 'secondary';
if (Configure::check('BechlemConnectLight.settings.backendBoxColor')):
    $backendBoxColor = Configure::read('BechlemConnectLight.settings.backendBoxColor');
endif;

// Title
$this->assign('title', $this->BechlemConnectLight->readCamel($this->getRequest()->getParam('controller'))
    . ' :: '
    . ucfirst($this->BechlemConnectLight->readCamel($this->getRequest()->getParam('action')))
);
// Breadcrumb
$this->Breadcrumbs->add([
    ['title' => __d('bechlem_connect_light', 'Dashboard')]
]); ?>
<?php if (isset($bechlemConnectDemoData) && ($bechlemConnectDemoData == 1) && !empty($bechlemConnectConfigConnectData->id)): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-danger" role="alert">
            <?= __d('bechlem_connect_light', 'The current Bechlem Connect Config is running on the Bechlem GmbH API Version 1.2 Demo data.'); ?>
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'Please update the default config with your license credentials (username and password).'),
                [
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'BechlemConnectConfigs',
                    'action'        => 'edit',
                    'id'            => h($bechlemConnectConfigConnectData->id),
                ],
                [
                    'class'         => 'alert-link text-light',
                    'title'         => __d('bechlem_connect_light', 'Update default config'),
                    'data-toggle'   => 'tooltip',
                    'escape'        => false,
                ]); ?>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                        <?= __d('bechlem_connect_light', 'Bechlem Product Manufacturers'); ?>
                        <small>(<?= __d('bechlem_connect_light', 'based on matched Reseller Items'); ?>)</small>
                    </h3>
                    <?= $this->Html->link(
                        __d('bechlem_connect_light', 'View products')
                        . ' '
                        . $this->Html->icon('arrow-circle-right'),
                        [
                            'prefix'        => 'Admin',
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'BechlemProducts',
                            'action'        => 'index',
                        ],
                        ['escapeTitle'   => false]); ?>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex">
                    <p class="d-flex flex-column">
                        <span class="text-bold text-lg">
                            <?= empty($bechlemProductManufacturersCount)? 0: h($bechlemProductManufacturersCount); ?>
                            <?= __d('bechlem_connect_light', 'manufacturers'); ?>
                        </span>
                        <span><?= __d('bechlem_connect_light', 'Products by manufacturer'); ?></span>
                    </p>
                </div>
                <div class="position-relative mb-4">
                    <canvas id="bechlem-product-manufacturers-chart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                        <?= __d('bechlem_connect_light', 'Bechlem Product Types'); ?>
                        <small>(<?= __d('bechlem_connect_light', 'based on matched Reseller Items'); ?>)</small>
                    </h3>
                    <?= $this->Html->link(
                        __d('bechlem_connect_light', 'View products')
                        . ' '
                        . $this->Html->icon('arrow-circle-right'),
                        [
                            'prefix'        => 'Admin',
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'BechlemProducts',
                            'action'        => 'index',
                        ],
                        ['escapeTitle'   => false]); ?>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex">
                    <p class="d-flex flex-column">
                        <span class="text-bold text-lg">
                            <?= empty($bechlemProductTypesCount)? 0: h($bechlemProductTypesCount); ?>
                            <?= __d('bechlem_connect_light', 'types'); ?>
                        </span>
                        <span><?= __d('bechlem_connect_light', 'Products by type'); ?></span>
                    </p>
                </div>
                <div class="position-relative mb-4">
                    <canvas id="bechlem-product-types-chart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-<?= h($backendBoxColor); ?>">
            <div class="inner">
                <h3><?= empty($domainsCount)? 0: h($domainsCount); ?></h3>
                <p><?= __d('bechlem_connect_light', 'Active'); ?> <?= __d('bechlem_connect_light', 'Domains'); ?></p>
            </div>
            <div class="icon"><?= $this->Html->icon('globe'); ?></div>
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'More info')
                . ' '
                . $this->Html->icon('arrow-circle-right'),
                [
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Domains',
                    'action'        => 'index',
                ],
                [
                    'class'         => 'small-box-footer',
                    'escapeTitle'   => false,
                ]); ?>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-<?= h($backendBoxColor); ?>">
            <div class="inner">
                <h3><?= empty($localesCount)? 0: h($localesCount); ?></h3>
                <p><?= __d('bechlem_connect_light', 'Active'); ?> <?= __d('bechlem_connect_light', 'Locales'); ?></p>
            </div>
            <div class="icon"><?= $this->Html->icon('list'); ?></div>
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'More info')
                . ' '
                . $this->Html->icon('arrow-circle-right'),
                [
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Locales',
                    'action'        => 'index',
                ],
                [
                    'class'         => 'small-box-footer',
                    'escapeTitle'   => false,
                ]); ?>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-<?= h($backendBoxColor); ?>">
            <div class="inner">
                <h3><?= empty($countriesCount)? 0: h($countriesCount); ?></h3>
                <p><?= __d('bechlem_connect_light', 'Active'); ?> <?= __d('bechlem_connect_light', 'Countries'); ?></p>
            </div>
            <div class="icon"><?= $this->Html->icon('list'); ?></div>
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'More info')
                . ' '
                . $this->Html->icon('arrow-circle-right'),
                [
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Countries',
                    'action'        => 'index',
                ],
                [
                    'class'         => 'small-box-footer',
                    'escapeTitle'   => false,
                ]); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-<?= h($backendBoxColor); ?>">
            <div class="inner">
                <h3><?= empty($registrationsCount)? 0: h($registrationsCount); ?></h3>
                <p><?= __d('bechlem_connect_light', 'Registrations'); ?></p>
            </div>
            <div class="icon"><?= $this->Html->icon('user-plus'); ?></div>
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'More info')
                . ' '
                . $this->Html->icon('arrow-circle-right'),
                [
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Registrations',
                    'action'        => 'index',
                ],
                [
                    'class'         => 'small-box-footer',
                    'escapeTitle'   => false,
                ]); ?>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-<?= h($backendBoxColor); ?>">
            <div class="inner">
                <h3><?= empty($rolesCount)? 0: h($rolesCount); ?></h3>
                <p><?= __d('bechlem_connect_light', 'Roles'); ?></p>
            </div>
            <div class="icon"><?= $this->Html->icon('users'); ?></div>
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'More info')
                . ' '
                . $this->Html->icon('arrow-circle-right'),
                [
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Roles',
                    'action'        => 'index',
                ],
                [
                    'class'         => 'small-box-footer',
                    'escapeTitle'   => false,
                ]); ?>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-<?= h($backendBoxColor); ?>">
            <div class="inner">
                <h3><?= empty($usersCount)? 0: h($usersCount); ?></h3>
                <p><?= __d('bechlem_connect_light', 'Active'); ?> <?= __d('bechlem_connect_light', 'Users'); ?></p>
            </div>
            <div class="icon"><?= $this->Html->icon('users'); ?></div>
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'More info')
                . ' '
                . $this->Html->icon('arrow-circle-right'),
                [
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Users',
                    'action'        => 'index',
                ],
                [
                    'class'         => 'small-box-footer',
                    'escapeTitle'   => false,
                ]); ?>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-<?= h($backendBoxColor); ?>">
            <div class="inner">
                <h3><?= empty($userProfilesCount)? 0: h($userProfilesCount); ?></h3>
                <p><?= __d('bechlem_connect_light', 'Active'); ?> <?= __d('bechlem_connect_light', 'User Profiles'); ?></p>
            </div>
            <div class="icon"><?= $this->Html->icon('users'); ?></div>
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'More info')
                . ' '
                . $this->Html->icon('arrow-circle-right'),
                [
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'UserProfiles',
                    'action'        => 'index',
                ],
                [
                    'class'         => 'small-box-footer',
                    'escapeTitle'   => false,
                ]); ?>
        </div>
    </div>
</div>

<?php if (isset($bechlemConnectDemoData) && ($bechlemConnectDemoData == 1)): ?>
<div class="modal fade" id="bechlemConnectDemoDataModal" tabindex="-1" aria-labelledby="bechlemConnectDemoDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bechlemConnectDemoDataModalLabel">
                    <?= __d('bechlem_connect_light', 'Welcome to the BECHLEM CONNECT "LIGHT" open source application and middleware for free!'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= __d('bechlem_connect_light', 'Close'); ?>">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <p>
                    <?= __d('bechlem_connect_light', 'This application is a middleware based on CakePHP 5 and PHP 8.1+ for the Bechlem API Interface based on the version 1.2.'); ?><br />
                    <?= __d('bechlem_connect_light', 'Developed as an open source software by the Marks Software GmbH based on the MIT License.'); ?><br />
                    <br />
                    <?= __d('bechlem_connect_light', 'Introduction from the bechlem.com API interface page'); ?>:<br />
                    <br />
                    <?= __d('bechlem_connect_light', 'Bringing in the best quality products at great rates are important for the success of your business!'); ?><br />
                    <?= __d('bechlem_connect_light', 'Excellent data interfaces and their integration into system landscapes are playing a major role in businesses today.'); ?><br />
                    <br />
                    <?= __d('bechlem_connect_light', 'Due to our more than 25 years experience in the printer supplies business, and cooperations with the top leaders of this sector compiled in our products, you can profit and share.'); ?><br />
                    <br />
                    <?= __d('bechlem_connect_light', 'Our API data interface links your system to'); ?>:
                    <ul>
                        <li><?= __d('bechlem_connect_light', 'more than 91.000 printers'); ?></li>
                        <li><?= __d('bechlem_connect_light', 'more than 153.000 supplies'); ?></li>
                        <li><?= __d('bechlem_connect_light', 'more than 19 million CrossSelling-Links'); ?></li>
                        <li><?= __d('bechlem_connect_light', 'advanced master data'); ?></li>
                        <li><?= __d('bechlem_connect_light', 'images'); ?></li>
                        <li><?= __d('bechlem_connect_light', 'and more'); ?></li>
                    </ul>
                    <?= __d('bechlem_connect_light', 'and will unlock new products and sales opportunities. The interface is fully digitalized and provides topical data in various structures and types.'); ?><br />
                    <br />
                    <?= __d('bechlem_connect_light', 'And last but not least, our services will help you concentrate on the most important factor: your business.'); ?><br />
                    <br />
                </p>
            </div>
            <div class="modal-footer">
                <?= $this->Html->link(
                    __d('bechlem_connect_light', 'I need the Bechlem GmbH API interface license!'),
                    'https://www.bechlem.de/241-2/',
                    [
                        'type'      => 'button',
                        'class'     => 'btn btn-warning',
                        'title'     => __d('bechlem_connect_light', 'Go to the Bechlem GmbH contact form'),
                        'target'    => '_blank',
                        'escape'    => false,
                    ]); ?>
                <?= $this->Html->link(
                    __d('bechlem_connect_light', 'I have already the Bechlem GmbH API interface license!'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'BechlemConnectConfigs',
                        'action'        => 'edit',
                        'id'            => h($bechlemConnectConfigConnectData->id),
                    ],
                    [
                        'type'      => 'button',
                        'class'     => 'btn btn-success',
                        'title'     => __d('bechlem_connect_light', 'Update default config'),
                        'target'    => '_self',
                        'escape'    => false,
                    ]); ?>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->scriptBlock(
    '$(function() {
        $(\'#bechlemConnectDemoDataModal\').modal({show: true});
    });',
    ['block' => 'scriptBottom']); ?>
<?php endif; ?>

<?php $bechlemProductManufacturersChartDataLabels = ''; ?>
<?php $bechlemProductManufacturersChartData = ''; ?>
<?php foreach ($bechlemProductManufacturers as $bechlemProductManufacturer): ?>
    <?php $bechlemProductManufacturersChartDataLabels .= '\'' . h($bechlemProductManufacturer->manufacturer_name) . '\','; ?>
    <?php $bechlemProductManufacturersChartData .= '' . h($bechlemProductManufacturer->manufacturer_product_count) . ','; ?>
<?php endforeach; ?>

<?php $bechlemProductTypesChartDataLabels = ''; ?>
<?php $bechlemProductTypesChartData = ''; ?>
<?php foreach ($bechlemProductTypes as $bechlemProductType): ?>
    <?php $bechlemProductTypesChartDataLabels .= '\'' . h($bechlemProductType->product_type_name) . '\','; ?>
    <?php $bechlemProductTypesChartData .= '' . h($bechlemProductType->type_product_count) . ','; ?>
<?php endforeach; ?>

<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'chart.js' . DS . 'Chart.min.js',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        var ticksStyle = {
            fontColor: \'#495057\',
            fontStyle: \'bold\'
        }
        var mode = \'index\';
        var intersect = true;
        var $bechlemProductManufacturersChart = $(\'#bechlem-product-manufacturers-chart\')
        var bechlemProductManufacturersChart = new Chart($bechlemProductManufacturersChart, {
            type: \'bar\',
            data: {
                labels: [' . substr($bechlemProductManufacturersChartDataLabels, 0, -1) . '],
                datasets: [
                    {
                        backgroundColor: \'#d81b60\',
                        borderColor: \'#d81b60\',
                        data: [' . substr($bechlemProductManufacturersChartData, 0, -1) . ']
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: { display: false },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: true,
                            lineWidth: \'4px\',
                            color: \'rgba(0, 0, 0, .2)\',
                            zeroLineColor: \'transparent\'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            callback: function (value) {
                                if (value >= 1000) {
                                    value /= 1000
                                    value += \'k\'
                                }
                                return value
                            }
                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })

        var $bechlemProductTypesChart = $(\'#bechlem-product-types-chart\')
        var bechlemProductTypesChart = new Chart($bechlemProductTypesChart, {
            type: \'bar\',
            data: {
                labels: [' . substr($bechlemProductTypesChartDataLabels, 0, -1) . '],
                datasets: [
                    {
                        backgroundColor: \'#d81b60\',
                        borderColor: \'#d81b60\',
                        data: [' . substr($bechlemProductTypesChartData, 0, -1) . ']
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: { display: false },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: true,
                            lineWidth: \'4px\',
                            color: \'rgba(0, 0, 0, .2)\',
                            zeroLineColor: \'transparent\'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            callback: function (value) {
                                if (value >= 1000) {
                                    value /= 1000
                                    value += \'k\'
                                }
                                return value
                            }
                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })
    });',
    ['block' => 'scriptBottom']); ?>