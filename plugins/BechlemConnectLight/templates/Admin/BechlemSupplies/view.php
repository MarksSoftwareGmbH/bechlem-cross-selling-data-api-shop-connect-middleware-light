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
    . h($bechlemSupply->id_item)
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
            'controller'    => 'BechlemSupplies',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'View')],
    ['title' => h($bechlemSupply->id_item)]
]); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= h($bechlemSupply->id_item); ?> - <?= __d('bechlem_connect_light', 'View'); ?>
                </h3>
                <div class="card-tools">
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'BechlemSupplies',
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
                                    'controller'    => 'BechlemSupplies',
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
                    <dd class="col-sm-9"><?= h($bechlemSupply->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id item'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->id_item)? '-': h($bechlemSupply->id_item); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id brand'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->id_brand)? '-': h($bechlemSupply->id_brand); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Brand'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->brand)? '-': h($bechlemSupply->brand); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Art nr'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->art_nr)? '-': h($bechlemSupply->art_nr); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Part nr'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->part_nr)? '-': h($bechlemSupply->part_nr); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->name)? '-': h($bechlemSupply->name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id category'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->id_category)? '-': h($bechlemSupply->id_category); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Category'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->category)? '-': h($bechlemSupply->category); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Color'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->color)? '-': h($bechlemSupply->color); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Is compatible'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->is_compatible)? '-': h($bechlemSupply->is_compatible); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'VE'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->ve)? '-': h($bechlemSupply->ve); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Yield'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->yield)? '-': h($bechlemSupply->yield); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Coverage'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->coverage)? '-': h($bechlemSupply->coverage); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Measures'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->measures)? '-': h($bechlemSupply->measures); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Content'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->content)? '-': h($bechlemSupply->content); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Content ml'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->content_ml)? '-': h($bechlemSupply->content_ml); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Content gram'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->content_gram)? '-': h($bechlemSupply->content_gram); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Content char'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->content_char)? '-': h($bechlemSupply->content_char); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'German group no'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->german_group_no)? '-': h($bechlemSupply->german_group_no); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Supply series'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->supply_series)? '-': h($bechlemSupply->supply_series); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'EAN'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->ean)? '-': h($bechlemSupply->ean); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Picture'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->picture)? '-': h($bechlemSupply->picture); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Language'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->language)? '-': h($bechlemSupply->language); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->created)? '-': h($bechlemSupply->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemSupply->modified)? '-': h($bechlemSupply->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
                <hr/>
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Picture'); ?></dt>
                    <dd><?= empty($bechlemSupply->picture)? '-': $this->BechlemConnectLight->bechlemCsdPicture(h($bechlemSupply->picture)); ?></dd>
                </dl>
                <hr/>
                <dl>
                    <dd>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="heading">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                                            <?= __d('bechlem_connect_light', 'REST API request') . ':' . ' ' . '/api/bechlem-supplies/' . h($bechlemSupply->id) . ' ' . '(' . __d('bechlem_connect_light', 'JSON decoded version') . ')'; ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <pre>
                                            <code class="language-php">
                                                <?php $json = json_encode(['success' => true, 'data' => $bechlemSupply]); ?>
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
                        'controller'    => 'BechlemSupplies',
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