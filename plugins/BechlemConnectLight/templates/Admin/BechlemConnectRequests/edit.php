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
    . ' :: '
    . $bechlemConnectRequest->name
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
            'controller'    => 'BechlemConnectRequests',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'Edit request')],
    ['title' => $bechlemConnectRequest->name]
]); ?>
<?= $this->Form->create($bechlemConnectRequest, ['class' => 'form-general']); ?>
<div class="row">
    <section class="col-lg-8 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $this->Html->icon('edit'); ?> <?= __d('bechlem_connect_light', 'Edit request'); ?>
                </h3>
            </div>
            <div class="card-body">
                <?= $this->Form->control('name', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('slug', [
                    'type'      => 'text',
                    'class'     => 'slug',
                    'required'  => true,
                    'readonly'  => true,
                ]); ?>
                <?= $this->Form->control('url', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('data', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('language', [
                    'type'      => 'select',
                    'label'     => __d('bechlem_connect_light', 'Language'),
                    'options'   => !empty($this->BechlemConnectLight->localeShortCodeNativeList())? $this->BechlemConnectLight->localeShortCodeNativeList(): [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => true,
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('options', [
                    'type' => 'text',
                ]); ?>
                <?= $this->Form->control('description', [
                    'type' => 'textarea',
                ]); ?>
                <?= $this->Form->control('example', [
                    'type' => 'textarea',
                ]); ?>
            </div>
        </div>
    </section>
    <section class="col-lg-4 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $this->Html->icon('cog'); ?> <?= __d('bechlem_connect_light', 'Actions'); ?>
                </h3>
            </div>
            <div class="card-body">
                <?= $this->Form->control('bechlem_connect_config_id', [
                    'label'     => __d('bechlem_connect_light', 'Config'),
                    'options'   => !empty($bechlemConnectConfigs)? $bechlemConnectConfigs: [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => false,
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('method', [
                    'label'     => __d('bechlem_connect_light', 'Method'),
                    'options'   => !empty($this->BechlemConnectLight->httpRequestTypes())? $this->BechlemConnectLight->httpRequestTypes(): [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => false,
                    'required'  => true,
                ]); ?>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?php $log = $bechlemConnectRequest->log? true: false; ?>
                        <?= $this->Form->checkbox('log', ['id' => 'log', 'class' => 'custom-control-input', 'checked' => $log, 'required' => false]); ?>
                        <label class="custom-control-label" for="log"><?= __d('bechlem_connect_light', 'Log'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?php $status = $bechlemConnectRequest->status? true: false; ?>
                        <?= $this->Form->checkbox('status', ['id' => 'status', 'class' => 'custom-control-input', 'checked' => $status, 'required' => false]); ?>
                        <label class="custom-control-label" for="status"><?= __d('bechlem_connect_light', 'Status'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <?= $this->Form->button(__d('bechlem_connect_light', 'Submit'), ['class' => 'btn btn-success']); ?>
                    <?= $this->Html->link(
                        __d('bechlem_connect_light', 'Cancel'),
                        [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'BechlemConnectRequests',
                            'action'        => 'index',
                        ],
                        [
                            'class'     => 'btn btn-danger float-right',
                            'escape'    => false,
                        ]); ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->Form->end(); ?>

<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'summernote' . DS . 'summernote-bs4.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'select2' . DS . 'css' . DS . 'select2.min'); ?>
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'bechlem_connect_light.select2'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'select2' . DS . 'js' . DS . 'select2.full.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'slug' . DS . 'jquery.slug',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'template' . DS . 'admin' . DS . 'bechlemConnectRequests' . DS . 'form',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        BechlemConnectRequests.init();
        // Initialize select2
        $(\'.select2\').select2();
        // Initialize summernote
        $(\'.options\').summernote();
        $(\'.description\').summernote();
        $(\'.example\').summernote();
        $(\'.form-general\').submit(function(event) {
            $(\'.options\').summernote(\'destroy\');
            $(\'.description\').summernote(\'destroy\');
            $(\'.example\').summernote(\'destroy\');
        });
        $(\'.form-general\').validate({
            rules: {
                name: {
                    required: true
                },
                slug: {
                    required: true
                },
                method: {
                    required: true
                },
                url: {
                    required: true
                },
                data: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid name') . '\'
                },
                slug: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid slug') . '\'
                },
                method: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid method') . '\'
                },
                url: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid url') . '\'
                },
                data: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid data') . '\'
                }
            },
            errorElement: \'span\',
            errorPlacement: function (error, element) {
                error.addClass(\'invalid-feedback\');
                element.closest(\'.form-group\').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass(\'is-invalid\');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass(\'is-invalid\');
            }
        });
    });',
    ['block' => 'scriptBottom']); ?>
