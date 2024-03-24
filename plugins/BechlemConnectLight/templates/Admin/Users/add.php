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
    [
        'title' => $this->BechlemConnectLight->readCamel($this->getRequest()->getParam('controller')),
        'url' => [
            'plugin'        => 'BechlemConnectLight',
            'controller'    => 'Users',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'Add User')]
]); ?>

<?= $this->Form->create($user, ['class' => 'form-general']); ?>
<div class="row">
    <section class="col-lg-8 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $this->Html->icon('plus'); ?> <?= __d('bechlem_connect_light', 'Add User'); ?>
                </h3>
            </div>
            <div class="card-body">
                <?= $this->Form->control('foreign_key', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('username', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('name', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('email', [
                    'type'      => 'email',
                    'required'  => true,
                ]); ?>
                <hr/>
                <?= $this->Form->control('password', [
                    'type'      => 'password',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('verify_password', [
                    'type'      => 'password',
                    'required'  => true,
                ]); ?>
                <p>
                    <?= __d('bechlem_connect_light', 'Password proposal'); ?>:
                    <span id="passwordProposal"><?= $this->BechlemConnectLight->generateStrongPassword(); ?></span>
                    (<span id="usePasswordProposal"><?= __d('bechlem_connect_light', 'use'); ?></span>)
                </p>
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
                <?= $this->Form->control('role_id', [
                    'type'      => 'select',
                    'label'     => __d('bechlem_connect_light', 'Role'),
                    'options'   => !empty($roles)? $roles: [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => true,
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('locale_id', [
                    'type'      => 'select',
                    'label'     => __d('bechlem_connect_light', 'Locale'),
                    'options'   => !empty($locales)? $locales: [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => true,
                    'required'  => true,
                ]); ?>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?= $this->Form->checkbox('status', ['id' => 'status', 'class' => 'custom-control-input', 'checked' => true, 'required' => false]); ?>
                        <label class="custom-control-label" for="status"><?= __d('bechlem_connect_light', 'Status'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <?= $this->Form->button(__d('bechlem_connect_light', 'Submit'), ['class' => 'btn btn-success']); ?>
                    <?= $this->Html->link(
                        __d('bechlem_connect_light', 'Cancel'),
                        [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'Users',
                            'action'        => 'index',
                        ],
                        [
                            'class'         => 'btn btn-danger float-right',
                            'escapeTitle'   => false,
                        ]); ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->Form->end(); ?>

<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'select2' . DS . 'css' . DS . 'select2.min'); ?>
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'bechlem_connect_light.select2'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'select2' . DS . 'js' . DS . 'select2.full.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'template' . DS . 'admin' . DS . 'users' . DS . 'form',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        Users.init();
        $(\'#usePasswordProposal\').click(function(event) {
            $(\'#password\').val($(\'#passwordProposal\').html());
            $(\'#verify-password\').val($(\'#passwordProposal\').html());
        });
        // Initialize select2
        $(\'.select2\').select2();
        $(\'.form-general\').validate({
            rules: {
                username: {
                    required: true
                },
                name: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true
                }
            },
            messages: {
                username: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid username') . '\'
                },
                name: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid name') . '\'
                },
                email: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid email address') . '\'
                },
                password: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid password') . '\'
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
