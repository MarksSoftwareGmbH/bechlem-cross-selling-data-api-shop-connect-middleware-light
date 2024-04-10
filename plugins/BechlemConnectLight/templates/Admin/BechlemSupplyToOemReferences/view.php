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
    . h($bechlemSupplyToOemReference->id_item_supply)
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
            'controller'    => 'BechlemSupplyToOemReferences',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'View')],
    ['title' => h($bechlemSupplyToOemReference->id_item_supply)]
]); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= h($bechlemSupplyToOemReference->id_item_supply); ?> - <?= __d('bechlem_connect_light', 'View'); ?>
                </h3>
                <div class="card-tools">
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'BechlemSupplyToOemReferences',
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
                                    'controller'    => 'BechlemSupplyToOemReferences',
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
                    <dd class="col-sm-9"><?= h($bechlemSupplyToOemReference->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id item supply'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->id_item_supply)? '-': h($bechlemSupplyToOemReference->id_item_supply); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id item supply oem'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->id_item_supply_oem)? '-': h($bechlemSupplyToOemReference->id_item_supply_oem); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id brand'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->id_brand)? '-': h($bechlemSupplyToOemReference->id_brand); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Brand'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->brand)? '-': h($bechlemSupplyToOemReference->brand); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Art nr'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->art_nr)? '-': h($bechlemSupplyToOemReference->art_nr); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Part nr'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->part_nr)? '-': h($bechlemSupplyToOemReference->part_nr); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Yield'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->yield)? '-': h($bechlemSupplyToOemReference->yield); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Content ml'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->content_ml)? '-': h($bechlemSupplyToOemReference->content_ml); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->created)? '-': h($bechlemSupplyToOemReference->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupplyToOemReference->modified)? '-': h($bechlemSupplyToOemReference->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
                <hr/>
                <dl>
                    <dd>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="heading">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                                            <?= __d('bechlem_connect_light', 'REST API request') . ':' . ' ' . '/api/bechlem-supply-to-oem-references/' . h($bechlemSupplyToOemReference->id) . ' ' . '(' . __d('bechlem_connect_light', 'JSON decoded version') . ')'; ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <pre>
                                            <code class="language-php">
                                                <?php $json = json_encode(['success' => true, 'data' => $bechlemSupplyToOemReference]); ?>
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
                        'controller'    => 'BechlemSupplyToOemReferences',
                        'action'        => 'index',
                    ],
                    [
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