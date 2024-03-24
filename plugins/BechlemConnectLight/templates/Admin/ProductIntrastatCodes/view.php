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
    . h($productIntrastatCode->code)
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
            'controller'    => 'ProductIntrastatCodes',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'View')],
    ['title' => h($productIntrastatCode->code)]
]); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= h($productIntrastatCode->code); ?> - <?= __d('bechlem_connect_light', 'View'); ?>
                </h3>
                <div class="card-tools">
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'ProductIntrastatCodes',
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
                                    'controller'    => 'ProductIntrastatCodes',
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
                    <dd class="col-sm-9"><?= h($productIntrastatCode->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Foreign Key'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productIntrastatCode->foreign_key)? '-': h($productIntrastatCode->foreign_key); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Code'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productIntrastatCode->code)? '-': h($productIntrastatCode->code); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Type'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productIntrastatCode->type)? '-': h($productIntrastatCode->type); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productIntrastatCode->name)? '-': h($productIntrastatCode->name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productIntrastatCode->created)? '-': h($productIntrastatCode->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= empty($productIntrastatCode->modified)? '-': h($productIntrastatCode->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
                <hr />
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Description'); ?></dt>
                    <dd><?= $this->Text->autoParagraph($productIntrastatCode->description); ?></dd>
                </dl>
                <hr/>
                <dl>
                    <dd>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="heading">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                                            <?= __d('bechlem_connect_light', 'REST API request') . ':' . ' ' . '/api/product-intrastat-codes/' . h($productIntrastatCode->id) . ' ' . '(' . __d('bechlem_connect_light', 'JSON decoded version') . ')'; ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <pre>
                                            <code class="language-php">
                                                <?php $json = json_encode(['success' => true, 'data' => $productIntrastatCode]); ?>
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
                        'controller'    => 'ProductIntrastatCodes',
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
                        'controller'    => 'ProductIntrastatCodes',
                        'action'        => 'edit',
                        'id'            => h($productIntrastatCode->id),
                    ],
                    [
                        'class'     => 'btn btn-app',
                        'escape'    => false,
                    ]); ?>
                <?= $this->Form->postLink(
                    $this->Html->icon('trash') . ' ' . __d('bechlem_connect_light', 'Delete'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'ProductIntrastatCodes',
                        'action'        => 'delete',
                        'id'            => h($productIntrastatCode->id),
                    ],
                    [
                        'confirm' => __d(
                            'bechlem_connect_light',
                            'Are you sure you want to delete "{code}"?',
                            ['code' => h($productIntrastatCode->code)]
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