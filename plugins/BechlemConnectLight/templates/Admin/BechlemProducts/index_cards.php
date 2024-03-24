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
                        'controller'    => 'BechlemProducts',
                        'action'        => 'indexCards',
                    ],
                ]); ?>
                <?= $this->Form->control('search', [
                    'type'          => 'text',
                    'value'         => $this->getRequest()->getQuery('search'),
                    'label'         => false,
                    'placeholder'   => __d('bechlem_connect_light', 'Search') . '...',
                    'prepend'       => $this->Form->postLink(
                        $this->Html->icon('cloud-download-alt') . ' ' . __d('bechlem_connect_light', 'Load all'),
                        [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'BechlemProducts',
                            'action'        => 'updateAll',
                        ],
                        [
                            'confirm'   => __d('bechlem_connect_light', 'Are you sure you want update all products?'),
                            'block'     => true,
                            'class'     => 'run',
                            'escape'    => false,
                        ]
                    ),
                    'append' => $this->Form->button(
                            __d('bechlem_connect_light', 'Filter'),
                            ['class' => 'btn btn-default']
                        )
                        . ' '
                        . $this->Html->link(
                            __d('bechlem_connect_light', 'Reset'),
                            [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'BechlemProducts',
                                'action'        => 'indexCards',
                            ],
                            [
                                'class'     => 'btn btn-default',
                                'escape'    => false,
                            ]
                        ),
                ]); ?>
                <?= $this->Form->end(); ?>
                <?= $this->fetch('postLink'); ?>
            </div>
            <div class="card-body">

                <div class="row">
                    <?php foreach ($bechlemProducts as $bechlemProduct): ?>
                        <?php if (empty($bechlemProduct->image)): continue; endif; ?>
                        <div class="col-md-12 col-lg-6 col-xl-4">
                            <div class="card mb-2 bg-gradient-dark">
                                <?= $this->Html->link(
                                    $this->BechlemConnectLight->bechlemCsdPictureCard(h($bechlemProduct->image)),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'BechlemProducts',
                                        'action'        => 'view',
                                        'id'            => h($bechlemProduct->id),
                                    ],
                                    [
                                        'title'         => empty($bechlemProduct->product_name_with_manufacturer)? '-': h($bechlemProduct->product_name_with_manufacturer),
                                        'escape'        => false,
                                    ]); ?>
                                <div class="card-img-overlay d-flex flex-column justify-content-end">
                                    <div class="small-box bg-light">
                                        <div class="inner">
                                            <h5 class="card-title text-primary text-black">
                                            <?= $this->Html->link(
                                                empty($bechlemProduct->product_name_with_manufacturer)? '-': h($bechlemProduct->product_name_with_manufacturer),
                                                [
                                                    'plugin'        => 'BechlemConnectLight',
                                                    'controller'    => 'BechlemProducts',
                                                    'action'        => 'view',
                                                    'id'            => h($bechlemProduct->id),
                                                ],
                                                [
                                                    'title'         => __d('bechlem_connect_light', 'View'),
                                                    'data-toggle'   => 'tooltip',
                                                    'escape'        => false,
                                                ]); ?>
                                            </h5>
                                            <p class="card-text text-black pb-2 pt-1">
                                                <?= empty($bechlemProduct->short_description)? '-': h($bechlemProduct->short_description); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?= $this->element('paginator'); ?>
        </div>
    </div>
</div>

<?= $this->element('please_wait'); ?>

<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'template' . DS . 'admin' . DS . 'default',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        Default.init();
    });',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        $(\'.run\').on(\'click\', function () {
            $(\'#pleaseWait\').modal(\'show\');
        });
    });',
    ['block' => 'scriptBottom']); ?>
