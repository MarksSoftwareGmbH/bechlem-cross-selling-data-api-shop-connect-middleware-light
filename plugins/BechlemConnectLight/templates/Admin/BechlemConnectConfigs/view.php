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
    . $bechlemConnectConfig->title
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
            'controller'    => 'BechlemConnectConfigs',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'View')],
    ['title' => $bechlemConnectConfig->title]
]); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $bechlemConnectConfig->title; ?> - <?= __d('bechlem_connect_light', 'View'); ?>
                </h3>
                <div class="card-tools">
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'BechlemConnectConfigs',
                            'action'        => 'index'
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
                                    'controller'    => 'BechlemConnectConfigs',
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
                    <dd class="col-sm-9"><?= h($bechlemConnectConfig->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Title'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemConnectConfig->title)? '-': $bechlemConnectConfig->title; ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Alias'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemConnectConfig->alias)? '-': h($bechlemConnectConfig->alias); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Host'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemConnectConfig->host)? '-': h($bechlemConnectConfig->host); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Port'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemConnectConfig->port)? '-': h($bechlemConnectConfig->port); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Scheme'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemConnectConfig->scheme)? '-': h($bechlemConnectConfig->scheme); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Username'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemConnectConfig->username)? '-': h($bechlemConnectConfig->username); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Password'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemConnectConfig->password)? '-': h($bechlemConnectConfig->password); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Status'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($bechlemConnectConfig->status)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= h($bechlemConnectConfig->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= h($bechlemConnectConfig->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
                <hr/>
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Description'); ?></dt>
                    <dd><?= $this->Text->autoParagraph($bechlemConnectConfig->description); ?></dd>
                </dl>
                <hr/>
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Requests'); ?></dt>
                    <dd>
                        <div class="row">
                            <div class="col-md-6">
                                <ol>
                                    <?php foreach ($bechlemConnectConfig->bechlem_connect_requests as $request): ?>
                                        <li><?= $this->Html->link($request->name_method, [
                                                'plugin'        => 'BechlemConnectLight',
                                                'controller'    => 'BechlemConnectRequests',
                                                'action'        => 'view',
                                                'id'            => h($request->id),
                                            ]); ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>
                    </dd>
                </dl>
                <hr/>
                <dl>
                    <dd>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="heading">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                                            <?= __d('bechlem_connect_light', 'REST API request') . ':' . ' ' . '/api/bechlem-connect-configs/' . h($bechlemConnectConfig->id) . ' ' . '(' . __d('bechlem_connect_light', 'JSON decoded version') . ')'; ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <pre>
                                            <code class="language-php">
                                                <?php $json = json_encode(['success' => true, 'data' => $bechlemConnectConfig]); ?>
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
                        'controller'    => 'BechlemConnectConfigs',
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
                        'controller'    => 'BechlemConnectConfigs',
                        'action'        => 'edit',
                        'id'            => h($bechlemConnectConfig->id),
                    ],
                    [
                        'class'     => 'btn btn-app',
                        'escape'    => false,
                    ]); ?>
                <?= $this->Form->postLink(
                    $this->Html->icon('trash') . ' ' . __d('bechlem_connect_light', 'Delete'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'BechlemConnectConfigs',
                        'action'        => 'delete',
                        'id'            => h($bechlemConnectConfig->id),
                    ],
                    [
                        'confirm' => __d(
                            'bechlem_connect_light',
                            'Are you sure you want to delete "{title}"?',
                            ['title' => $bechlemConnectConfig->title]
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