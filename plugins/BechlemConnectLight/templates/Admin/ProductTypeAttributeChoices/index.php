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
                        'controller'    => 'ProductTypeAttributeChoices',
                        'action'        => 'index',
                    ],
                ]); ?>
                <?php $this->Form->setTemplates(['inputGroupText' => '{{content}}']); ?>
                <?= $this->Form->control('search', [
                    'type'          => 'text',
                    'value'         => $this->getRequest()->getQuery('search'),
                    'label'         => false,
                    'placeholder'   => __d('bechlem_connect_light', 'Search') . '...',
                    'prepend'       => $this->Html->link(
                        $this->Html->icon('plus') . ' ' . __d('bechlem_connect_light', 'Add type attribute choice'),
                        [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'ProductTypeAttributeChoices',
                            'action'        => 'add',
                        ],
                        [
                            'class'         => 'btn btn-' . h($backendButtonColor),
                            'escapeTitle'   => false,
                        ]),
                    'append' => $this->Form->button(
                            __d('bechlem_connect_light', 'Filter'),
                            ['class' => 'btn btn-' . h($backendButtonColor)]
                        )
                        . ' '
                        . $this->Html->link(
                            __d('bechlem_connect_light', 'Reset'),
                            [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'ProductTypeAttributeChoices',
                                'action'        => 'index',
                            ],
                            [
                                'class'     => 'btn btn-' . h($backendButtonColor),
                                'escape'    => false,
                            ]
                        )
                        . ' '
                        . $this->Html->link(
                            $this->Html->icon('upload') . ' ' . __d('bechlem_connect_light', 'CSV'),
                            [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'ProductTypeAttributeChoices',
                                'action'        => 'import',
                            ],
                            [
                                'class'     => 'btn btn-' . h($backendButtonColor),
                                'escape'    => false,
                                'title'     => __d('bechlem_connect_light', 'Upload & import csv'),
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
                                'controller'    => 'ProductTypeAttributeChoices',
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
                                'controller'    => 'ProductTypeAttributeChoices',
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
                                'controller'    => 'ProductTypeAttributeChoices',
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
                                'controller'    => 'ProductTypeAttributeChoices',
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
                        <th><?= $this->Paginator->sort('ProductTypeAttributes.alias', __d('bechlem_connect_light', 'Type attribute')); ?></th>
                        <th><?= $this->Paginator->sort('value', __d('bechlem_connect_light', 'Value')); ?></th>
                        <th class="actions"><?= __d('bechlem_connect_light', 'Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($productTypeAttributeChoices as $productTypeAttributeChoice): ?>
                        <tr>
                            <td>
                                <?php if (!empty($productTypeAttributeChoice->product_type_attribute->title_alias)): ?>
                                    <?= $productTypeAttributeChoice->has('product_type_attribute')?
                                    $this->Html->link($productTypeAttributeChoice->product_type_attribute->title_alias, [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'ProductTypeAttributes',
                                        'action'        => 'view',
                                        'id'            => h($productTypeAttributeChoice->product_type_attribute->id),
                                    ]): '-'; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= h($productTypeAttributeChoice->value); ?></td>
                            <td class="actions">
                                <?= $this->Html->link(
                                    $this->Html->icon('eye'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'ProductTypeAttributeChoices',
                                        'action'        => 'view',
                                        'id'            => h($productTypeAttributeChoice->id),
                                    ],
                                    [
                                        'title' => __d('bechlem_connect_light', 'View'),
                                        'data-toggle' => 'tooltip',
                                        'escape' => false,
                                    ]); ?>
                                <?= $this->Html->link(
                                    $this->Html->icon('edit'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'ProductTypeAttributeChoices',
                                        'action'        => 'edit',
                                        'id'            => h($productTypeAttributeChoice->id),
                                    ],
                                    [
                                        'title' => __d('bechlem_connect_light', 'Edit'),
                                        'data-toggle' => 'tooltip',
                                        'escape' => false,
                                    ]); ?>
                                <?= $this->Form->postLink(
                                    $this->Html->icon('trash'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'ProductTypeAttributeChoices',
                                        'action'        => 'delete',
                                        'id'            => h($productTypeAttributeChoice->id),
                                    ],
                                    [
                                        'confirm' => __d(
                                            'bechlem_connect_light',
                                            'Are you sure you want to delete "{value}"?',
                                            ['value' => h($productTypeAttributeChoice->value)]
                                        ),
                                        'title' => __d('bechlem_connect_light', 'Delete'),
                                        'data-toggle' => 'tooltip',
                                        'escape' => false,
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
