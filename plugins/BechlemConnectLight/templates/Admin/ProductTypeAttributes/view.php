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
    . ' :: '
    . h($productTypeAttribute->title)
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
    [
        'title' => $this->BechlemConnectLight->readCamel($this->getRequest()->getParam('controller')),
        'url' => [
            'plugin'        => 'BechlemConnectLight',
            'controller'    => 'ProductTypeAttributes',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'View')],
    ['title' => h($productTypeAttribute->title)]
]); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= h($productTypeAttribute->title); ?> - <?= __d('bechlem_connect_light', 'View'); ?>
                </h3>
                <div class="card-tools">
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'ProductTypeAttributes',
                            'action'        => 'index',
                        ],
                    ]); ?>
                    <?= $this->Form->control('search', [
                        'type'          => 'text',
                        'label'         => false,
                        'placeholder'   => __d('bechlem_connect_light', 'Search') . '...',
                        'style'         => 'width: 150px;',
                        'append'        => $this->Form->button(
                                __d('bechlem_connect_light', 'Filter'),
                                ['class' => 'btn btn-' . h($backendButtonColor)]
                            )
                            . ' '
                            . $this->Html->link(
                                __d('bechlem_connect_light', 'Reset'),
                                [
                                    'plugin'        => 'BechlemConnectLight',
                                    'controller'    => 'ProductTypeAttributes',
                                    'action'        => 'index',
                                ],
                                [
                                    'class'     => 'btn btn-' . h($backendButtonColor),
                                    'escape'    => false,
                                ]
                            ),
                    ]); ?>
                    <?= $this->Form->end(); ?>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id'); ?></dt>
                    <dd class="col-sm-9"><?= h($productTypeAttribute->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Foreign key'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productTypeAttribute->foreign_key)? '-': h($productTypeAttribute->foreign_key); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Title'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productTypeAttribute->title)? '-': h($productTypeAttribute->title); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Alias'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productTypeAttribute->alias)? '-': h($productTypeAttribute->alias); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Empty value'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($productTypeAttribute->empty_value)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'WYSIWYG'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($productTypeAttribute->wysiwyg)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productTypeAttribute->created)? '-': h($productTypeAttribute->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productTypeAttribute->modified)? '-': h($productTypeAttribute->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
                <hr />
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Description'); ?></dt>
                    <dd><?= $this->Text->autoParagraph($productTypeAttribute->description); ?></dd>
                </dl>
                <hr />
                <?php if (!empty($productTypeAttribute->product_type_attribute_choices)): ?>
                    <dl>
                        <dt><?= __d('bechlem_connect_light', 'Type attribute choices'); ?></dt>
                        <dd>
                            <div class="row">
                                <div class="col-md-6">
                                    <ol>
                                        <?php foreach ($productTypeAttribute->product_type_attribute_choices as $choice): ?>
                                            <li><?= $this->Html->link(h($choice->value), [
                                                'plugin'        => 'BechlemConnectLight',
                                                'controller'    => 'ProductTypeAttributeChoices',
                                                'action'        => 'view',
                                                'id'            => h($choice->id),
                                            ]); ?></li>
                                        <?php endforeach; ?>
                                    </ol>
                                </div>
                            </div>
                        </dd>
                    </dl>
                    <hr/>
                <?php endif; ?>
                <?php if (!empty($productTypeAttribute->product_types)): ?>
                    <dl>
                        <dt><?= __d('bechlem_connect_light', 'Types'); ?></dt>
                        <dd>
                            <div class="row">
                                <div class="col-md-6">
                                    <ol>
                                        <?php foreach ($productTypeAttribute->product_types as $type): ?>
                                            <li><?= $this->Html->link(h($type->title), [
                                                'plugin'        => 'BechlemConnectLight',
                                                'controller'    => 'ProductTypes',
                                                'action'        => 'view',
                                                'id'            => h($type->id),
                                            ]); ?></li>
                                        <?php endforeach; ?>
                                    </ol>
                                </div>
                            </div>
                        </dd>
                    </dl>
                    <hr/>
                <?php endif; ?>
                <hr/>
                <dl>
                    <dd>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="heading">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                                            <?= __d('bechlem_connect_light', 'REST API request') . ':' . ' ' . '/api/product-type-attributes/' . h($productTypeAttribute->id) . ' ' . '(' . __d('bechlem_connect_light', 'JSON decoded version') . ')'; ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <pre>
                                            <code class="language-php">
                                                <?php $json = json_encode(['success' => true, 'data' => $productTypeAttribute]); ?>
                                                <?= print_r(json_decode($json), true); ?>
                                            </code>
                                        </pre>
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </dd>
                </dl>
                <hr/>
                <?= $this->Html->link(
                    $this->Html->icon('list') . ' ' . __d('bechlem_connect_light', 'Index'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'ProductTypeAttributes',
                        'action'        => 'index',
                    ],
                    [
                        'class'     => 'btn btn-app',
                        'escape'    => false,
                    ]); ?>
                <?= $this->Html->link(
                    $this->Html->icon('edit') . ' ' . __d('bechlem_connect_light', 'Edit'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'ProductTypeAttributes',
                        'action'        => 'edit',
                        'id'            => h($productTypeAttribute->id),
                    ],
                    [
                        'class' => 'btn btn-app',
                        'escape' => false,
                    ]) ?>
                <?= $this->Form->postLink(
                    $this->Html->icon('trash') . ' ' . __d('bechlem_connect_light', 'Delete'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'ProductTypeAttributes',
                        'action'        => 'delete',
                        'id'            => h($productTypeAttribute->id),
                    ],
                    [
                        'confirm' => __d(
                            'bechlem_connect_light',
                            'Are you sure you want to delete "{title}"?',
                            ['title' => h($productTypeAttribute->title)]
                        ),
                        'class'     => 'btn btn-app',
                        'escape'    => false,
                    ]); ?>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'prism' . DS . 'prism.min'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'prism' . DS . 'prism.min',
    ['block' => 'scriptBottom']); ?>