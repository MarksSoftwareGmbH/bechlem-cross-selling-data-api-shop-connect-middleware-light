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
                        'controller'    => 'Logs',
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
                        $this->Html->icon('plus') . ' ' . __d('bechlem_connect_light', 'Add log'),
                        [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'Logs',
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
                                'controller'    => 'Logs',
                                'action'        => 'index',
                            ],
                            [
                                'class'         => 'btn btn-' . h($backendButtonColor),
                                'escapeTitle'   => false,
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
                                'controller'    => 'Logs',
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
                                'controller'    => 'Logs',
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
                                'controller'    => 'Logs',
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
                                'controller'    => 'Logs',
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
                        <th><?= $this->Paginator->sort('request', __d('bechlem_connect_light', 'Request')); ?></th>
                        <th><?= $this->Paginator->sort('type', __d('bechlem_connect_light', 'Type')); ?></th>
                        <th><?= $this->Paginator->sort('message', __d('bechlem_connect_light', 'Message')); ?></th>
                        <th><?= $this->Paginator->sort('ip', __d('bechlem_connect_light', 'IP')); ?></th>
                        <th><?= $this->Paginator->sort('uri', __d('bechlem_connect_light', 'URI')); ?></th>
                        <th><?= $this->Paginator->sort('data', __d('bechlem_connect_light', 'Data')); ?></th>
                        <th><?= $this->Paginator->sort('created', __d('bechlem_connect_light', 'Created')); ?></th>
                        <th class="actions"><?= __d('bechlem_connect_light', 'Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= h($log->request); ?></td>
                            <td><?= h($log->type); ?></td>
                            <td><?= h($log->message); ?></td>
                            <td><?= h($log->ip); ?></td>
                            <td><?= $this->Text->truncate(
                                    h($log->uri),
                                    35,
                                    ['ellipsis' => '...', 'exact' => false]); ?></td>
                            <td>
                                <?= $this->Html->link(
                                    $this->Text->truncate(
                                        $log->data,
                                        35,
                                        ['ellipsis' => '...', 'exact' => false]
                                    ),
                                    '#',
                                    [
                                        'data-target'   => '#modal' . '-' . h($log->id),
                                        'data-toggle'   => 'modal',
                                        'escapeTitle'   => false,
                                    ]); ?>
                                <div
                                    class="modal fade"
                                    id="modal-<?= h($log->id); ?>"
                                    tabindex="-1"
                                    role="dialog"
                                    aria-labelledby="modal-<?= h($log->id); ?>-label"
                                    aria-hidden="true"
                                >
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5
                                                    class="modal-title"
                                                    id="modal-<?= h($log->id); ?>-label"
                                                >
                                                    <?= h($log->message) . ' ' . '(' . h($log->created) . ')' . ' ' . '(' . h($log->request) . ')'; ?>
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <strong><?= __d('bechlem_connect_light', 'JSON decoded'); ?></strong><br/>
                                                <pre>
                                                    <code class="language-php">
                                                        <?= print_r(json_decode($log->data), true); ?>
                                                    </code>
                                                </pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><?= empty($log->modified)? '-': h($log->modified->format('d.m.Y H:i:s')); ?></td>
                            <td class="actions">
                                <?= $this->Html->link(
                                    $this->Html->icon('eye'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'Logs',
                                        'action'        => 'view',
                                        'id'            => h($log->id),
                                    ],
                                    [
                                        'title'         => __d('bechlem_connect_light', 'View'),
                                        'data-toggle'   => 'tooltip',
                                        'escapeTitle'   => false,
                                    ]); ?>
                                <?= $this->Html->link(
                                    $this->Html->icon('edit'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'Logs',
                                        'action'        => 'edit',
                                        'id'            => h($log->id),
                                    ],
                                    [
                                        'title'         => __d('bechlem_connect_light', 'Edit'),
                                        'data-toggle'   => 'tooltip',
                                        'escapeTitle'   => false,
                                    ]); ?>
                                <?= $this->Form->postLink(
                                    $this->Html->icon('trash'),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'Logs',
                                        'action'        => 'delete',
                                        'id'            => h($log->id),
                                    ],
                                    [
                                        'confirm' => __d(
                                            'bechlem_connect_light',
                                            'Are you sure you want to delete "{request}"?',
                                            ['request' => h($log->request)]
                                        ),
                                        'title'         => __d('bechlem_connect_light', 'Delete'),
                                        'data-toggle'   => 'tooltip',
                                        'escapeTitle'   => false,
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

<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'prism' . DS . 'prism.min'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'prism' . DS . 'prism.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'template' . DS . 'admin' . DS . 'default',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        Default.init();
        $(\'.modal-wide\').on(\'show.bs.modal\', function() {
            var height = $(window).height() - 200;
            $(this).find(\'.modal-body\').css(\'max-height\', height);
        });
    });',
    ['block' => 'scriptBottom']); ?>
