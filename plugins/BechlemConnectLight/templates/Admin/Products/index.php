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

// Title
$this->assign('title', $this->BechlemConnectLight->readCamel($this->getRequest()->getParam('controller'))
    . ' :: '
    . ucfirst($this->BechlemConnectLight->readCamel($this->getRequest()->getParam('action')))
);
// Breadcrumb
$this->Breadcrumbs->add([
    [
        'title' => __d('bechlem_connect_light', 'Dashboard'),
        'url' => [
            'plugin'        => 'BechlemConnectLight',
            'controller'    => 'Dashboards',
            'action'        => 'dashboard',
        ]
    ],
    ['title' => $this->BechlemConnectLight->readCamel($this->getRequest()->getParam('controller'))]
]); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <?= $this->Form->create(null, [
                    'url' => [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Products',
                        'action'        => 'index',
                        '?'             => $this->getRequest()->getQuery(),
                    ],
                ]); ?>
                <?= $this->Form->control('search', [
                    'type'          => 'text',
                    'value'         => $this->getRequest()->getQuery('search'),
                    'label'         => false,
                    'placeholder'   => __d('bechlem_connect_light', 'Search') . '...',
                    'prepend'       => $this->element('Products' . DS . 'add_select_product_type', ['productTypes' => !empty($productTypes)? $productTypes: []])
                        . $this->element('Products' . DS . 'add_search_product_type', ['productTypes' => !empty($productTypes)? $productTypes: []])
                        . $this->element('Products' . DS . 'add_search_product_condition', ['productConditions' => !empty($productConditions)? $productConditions: []])
                        . $this->element('Products' . DS . 'add_search_product_manufacturer', ['productManufacturers' => !empty($productManufacturers)? $productManufacturers: []]),
                    'append' => $this->Form->button(
                            __d('bechlem_connect_light', 'Filter'),
                            ['class' => 'btn btn-' . h($backendButtonColor)]
                        )
                        . ' '
                        . $this->Html->link(
                            __d('bechlem_connect_light', 'Reset'),
                            [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'Products',
                                'action'        => 'index',
                            ],
                            [
                                'class'     => 'btn btn-' . h($backendButtonColor),
                                'escape'    => false,
                            ]
                        )
                        . ' '
                        . '<div class="btn-group dropleft">'
                        . $this->Html->link(
                            $this->Html->tag('span', '', ['class' => 'caret']) . ' ' . $this->Html->icon('download') . ' ' . __d('bechlem_connect_light', 'Download'),
                            '#',
                            [
                                'type'          => 'button',
                                'class'         => 'dropdown-toggle btn btn-' . h($backendButtonColor),
                                'id'            => 'dropdownMenu',
                                'data-toggle'   => 'dropdown',
                                'aria-haspopup' => true,
                                'aria-expanded' => false,
                                'escapeTitle'   => false,
                                'title'         => __d('bechlem_connect_light', 'Download'),
                            ]
                        )
                        . '<div class="dropdown-menu" aria-labelledby="dropdownMenu">'
                        . $this->Html->link(
                            __d('bechlem_connect_light', 'XLSX'),
                            [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'Products',
                                'action'        => 'exportXlsx',
                            ],
                            [
                                'class'         => 'dropdown-item',
                                'escapeTitle'   => false,
                                'title'         => __d('bechlem_connect_light', 'Export & download XLSX'),
                            ])
                        . $this->Html->link(
                            __d('bechlem_connect_light', 'CSV'),
                            [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'Products',
                                'action'        => 'exportCsv',
                                '_ext'          => 'csv',
                            ],
                            [
                                'class'         => 'dropdown-item',
                                'escapeTitle'   => false,
                                'title'         => __d('bechlem_connect_light', 'Export & download CSV'),
                            ])
                        . $this->Html->link(
                            __d('bechlem_connect_light', 'XML'),
                            [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'Products',
                                'action'        => 'exportXml',
                                '_ext'          => 'xml',
                            ],
                            [
                                'class'         => 'dropdown-item',
                                'escapeTitle'   => false,
                                'title'         => __d('bechlem_connect_light', 'Export & download XML'),
                            ])
                        . $this->Html->link(
                            __d('bechlem_connect_light', 'JSON'),
                            [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'Products',
                                'action'        => 'exportJson',
                                '_ext'          => 'json',
                            ],
                            [
                                'class'         => 'dropdown-item',
                                'escapeTitle'   => false,
                                'title'         => __d('bechlem_connect_light', 'Export & download JSON'),
                            ])
                        . '</div>'
                        . '</div>',
                ]); ?>
                <?= $this->Form->end(); ?>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover text-nowrap">
                    <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('ProductTypes.title', __d('bechlem_connect_light', 'Type')); ?></th>
                        <th><?= $this->Paginator->sort('ProductConditions.title', __d('bechlem_connect_light', 'Condition')); ?></th>
                        <th><?= $this->Paginator->sort('ProductManufacturers.title', __d('bechlem_connect_light', 'Manufacturer')); ?></th>
                        <th><?= $this->Paginator->sort('foreign_key', __d('bechlem_connect_light', 'Foreign key')); ?></th>
                        <th><?= $this->Paginator->sort('sku', __d('bechlem_connect_light', 'SKU')); ?></th>
                        <th><?= $this->Paginator->sort('manufacturer_sku', __d('bechlem_connect_light', 'OEM')); ?></th>
                        <th><?= $this->Paginator->sort('ean', __d('bechlem_connect_light', 'EAN')); ?></th>
                        <th><?= $this->Paginator->sort('name', __d('bechlem_connect_light', 'Name')); ?></th>
                        <th><?= $this->Paginator->sort('promote', __d('bechlem_connect_light', 'Promote')); ?></th>
                        <th><?= $this->Paginator->sort('promote_new', __d('bechlem_connect_light', 'Promote new')); ?></th>
                        <th><?= $this->Paginator->sort('status', __d('bechlem_connect_light', 'Status')); ?></th>
                        <th><?= $this->Paginator->sort('modified', __d('bechlem_connect_light', 'Modified')); ?></th>
                        <th><?= __d('bechlem_connect_light', 'Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?php if (!empty($product->product_type->title)): ?>
                                    <?= $product->has('product_type')?
                                        $this->Html->link(
                                            h($product->product_type->title),
                                            [
                                                'plugin'        => 'BechlemConnectLight',
                                                'controller'    => 'ProductTypes',
                                                'action'        => 'view',
                                                'id'            => h($product->product_type->id),
                                            ]): '-'; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($product->product_condition->title)): ?>
                                    <?= $product->has('product_condition')?
                                        $this->Html->link(
                                            h($product->product_condition->title),
                                            [
                                                'plugin'        => 'BechlemConnectLight',
                                                'controller'    => 'ProductConditions',
                                                'action'        => 'view',
                                                'id'            => h($product->product_condition->id),
                                            ]): '-'; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($product->product_manufacturer->name)): ?>
                                    <?= $product->has('product_manufacturer')?
                                        $this->Html->link(
                                            h($product->product_manufacturer->name),
                                            [
                                                'plugin'        => 'BechlemConnectLight',
                                                'controller'    => 'ProductManufacturers',
                                                'action'        => 'view',
                                                'id'            => h($product->product_manufacturer->id),
                                            ]): '-'; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= $product->foreign_key; ?></td>
                            <td><?= $product->sku; ?></td>
                            <td><?= $product->manufacturer_sku; ?></td>
                            <td><?= $product->ean; ?></td>
                            <td><?= $product->name; ?></td>
                            <td><?= $this->BechlemConnectLight->status(h($product->promote)); ?></td>
                            <td><?= $this->BechlemConnectLight->status(h($product->promote_new)); ?></td>
                            <td><?= $this->BechlemConnectLight->status(h($product->status)); ?></td>
                            <td><?= h($product->modified->format('d.m.Y H:i:s')); ?></td>
                            <td class="actions">
                                <?= $this->Html->link(
                                    $this->Html->icon('eye'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'Products',
                                        'action'        => 'view',
                                        'id'            => h($product->id),
                                    ],
                                    [
                                        'title'         => __d('bechlem_connect_light', 'View'),
                                        'data-toggle'   => 'tooltip',
                                        'escape'        => false,
                                    ]); ?>
                                <?= $this->Html->link(
                                    $this->Html->icon('edit'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'Products',
                                        'action'        => 'edit',
                                        'id'            => h($product->id),
                                    ],
                                    [
                                        'title' => __d('bechlem_connect_light', 'Edit'),
                                        'data-toggle' => 'tooltip',
                                        'escape' => false,
                                    ]); ?>
                                <?= $this->Form->postLink(
                                    $this->Html->icon('copy'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'Products',
                                        'action'        => 'copy',
                                        'id'            => h($product->id),
                                    ],
                                    [
                                        'title'         => __d('bechlem_connect_light', 'Copy'),
                                        'data-toggle'   => 'tooltip',
                                        'escape'        => false,
                                    ]); ?>
                                <?= $this->Form->postLink(
                                    $this->Html->icon('trash'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'Products',
                                        'action'        => 'delete',
                                        'id'            => h($product->id),
                                    ],
                                    [
                                        'confirm' => __d(
                                            'bechlem_connect_light',
                                            'Are you sure you want to delete "{name}"?',
                                            ['name' => h($product->name)]
                                        ),
                                        'title'         => __d('bechlem_connect_light', 'Delete'),
                                        'data-toggle'   => 'tooltip',
                                        'escape'        => false,
                                    ]); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= $this->element('paginator'); ?>
        </div>
    </div>
</div>

<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'template' . DS . 'admin' . DS . 'default',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        Default.init();
    });',
    ['block' => 'scriptBottom']); ?>
