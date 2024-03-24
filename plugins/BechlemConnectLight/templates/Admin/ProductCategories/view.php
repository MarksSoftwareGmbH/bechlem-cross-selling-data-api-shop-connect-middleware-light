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
    . $productCategory->name
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
            'controller'    => 'ProductCategories',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'View')],
    ['title' => $productCategory->name]
]); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $productCategory->name; ?> - <?= __d('bechlem_connect_light', 'View'); ?>
                </h3>
                <div class="card-tools">
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'ProductCategories',
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
                                    'controller'    => 'ProductCategories',
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
                    <dd class="col-sm-9"><?= h($productCategory->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Parent'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($productCategory->parent_product_category->name)): ?>
                            <?= $productCategory->has('parent_product_category')?
                            $this->Html->link(h($productCategory->parent_product_category->name), [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'ProductCategories',
                                'action'        => 'view',
                                'id'            => h($productCategory->parent_product_category->id),
                            ]): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Foreign Key'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productCategory->foreign_key)? '-': h($productCategory->foreign_key); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productCategory->name)? '-': h($productCategory->name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Slug'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productCategory->slug)? '-': h($productCategory->slug); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Background Image'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productCategory->background_image)? '-': h($productCategory->background_image); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Left'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productCategory->lft)? '-': $this->Number->format($productCategory->lft); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Right'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productCategory->rght)? '-': $this->Number->format($productCategory->rght); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Locale'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productCategory->locale)? '-': h($productCategory->locale); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Status'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($productCategory->status)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productCategory->created)? '-': h($productCategory->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productCategory->modified)? '-': h($productCategory->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
                <hr />
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Description'); ?></dt>
                    <dd><?= $this->Text->autoParagraph($productCategory->description); ?></dd>
                </dl>
                <hr />
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Meta Description'); ?></dt>
                    <dd><?= $this->Text->autoParagraph($productCategory->meta_description); ?></dd>
                </dl>
                <hr />
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Meta Keywords'); ?></dt>
                    <dd><?= $this->Text->autoParagraph($productCategory->meta_keywords); ?></dd>
                </dl>
                <hr/>
                <dl>
                    <dd>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="heading">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                                            <?= __d('bechlem_connect_light', 'REST API request') . ':' . ' ' . '/api/product-categories/' . h($productCategory->id) . ' ' . '(' . __d('bechlem_connect_light', 'JSON decoded version') . ')'; ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <pre>
                                            <code class="language-php">
                                                <?php $json = json_encode(['success' => true, 'data' => $productCategory]); ?>
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
                        'controller'    => 'ProductCategories',
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
                        'controller'    => 'ProductCategories',
                        'action'        => 'edit',
                        'id'            => h($productCategory->id),
                    ],
                    [
                        'class'     => 'btn btn-app',
                        'escape'    => false,
                    ]); ?>
                <?= $this->Form->postLink(
                    $this->Html->icon('trash') . ' ' . __d('bechlem_connect_light', 'Delete'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'ProductCategories',
                        'action'        => 'delete',
                        'id'            => h($productCategory->id),
                    ],
                    [
                        'confirm' => __d(
                            'bechlem_connect_light',
                            'Are you sure you want to delete "{name}"?',
                            ['name' => $productCategory->name]
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