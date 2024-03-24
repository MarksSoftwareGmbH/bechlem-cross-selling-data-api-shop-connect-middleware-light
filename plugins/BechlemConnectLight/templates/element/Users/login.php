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

$frontendLinkTextColor = 'navy';
if (Configure::check('BechlemConnectLight.settings.frontendLinkTextColor')):
    $frontendLinkTextColor = Configure::read('BechlemConnectLight.settings.frontendLinkTextColor');
endif;

$frontendButtonColor = 'secondary';
if (Configure::check('BechlemConnectLight.settings.frontendButtonColor')):
    $frontendButtonColor = Configure::read('BechlemConnectLight.settings.frontendButtonColor');
endif;

$frontendBoxColor = 'secondary';
if (Configure::check('BechlemConnectLight.settings.frontendBoxColor')):
    $frontendBoxColor = Configure::read('BechlemConnectLight.settings.frontendBoxColor');
endif;

// Title
$this->assign('title', $this->BechlemConnectLight->readCamel($this->getRequest()->getParam('controller'))
    . ' :: '
    . ucfirst($this->getRequest()->getParam('action'))
);
$this->Html->meta('robots', 'noindex, nofollow', ['block' => true]);
$this->Html->meta('author', 'Bechlem Connect Light', ['block' => true]);
$this->Html->meta('description', __d('bechlem_connect_light', 'Login'), ['block' => true]);

$this->Html->meta([
    'property'  => 'og:title',
    'content'   => __d('bechlem_connect_light', 'Login'),
    'block'     => 'meta',
]);
$this->Html->meta([
    'property'  => 'og:description',
    'content'   => __d('bechlem_connect_light', 'Login'),
    'block'     => 'meta',
]);
$this->Html->meta([
    'property'  => 'og:url',
    'content'   => $this->Url->build([
        'plugin'        => 'BechlemConnectLight',
        'controller'    => 'Users',
        'action'        => 'login',
    ], ['fullBase' => true]),
    'block' => 'meta',
]);
$this->Html->meta([
    'property'  => 'og:locale',
    'content'   => $session->read('Locale.code'),
    'block'     => 'meta',
]);
$this->Html->meta([
    'property'  => 'og:type',
    'content'   => 'website',
    'block'     => 'meta',
]);
$this->Html->meta([
    'property'  => 'og:site_name',
    'content'   => 'Bechlem Connect Light',
    'block'     => 'meta',
]); ?>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card">
            <div class="card-body login-card-body">
                <?= $this->element('flash'); ?>
                <div class="login-logo">
                    <?= $this->Html->link(
                        $this->Html->image(
                            'logo.png', [
                            'alt'   => __d('bechlem_connect_light', 'Logo'),
                            'class' => 'img-fluid',
                        ]),
                        '/',
                        ['escapeTitle' => false]); ?>
                </div>
                <p class="login-box-msg">
                    <?= __d('bechlem_connect_light', 'Welcome to {bechlemConnectLight}', ['bechlemConnectLight' => 'Bechlem Connect Light']); ?> - v<?= Configure::version(); ?>
                </p>

                <?php if ($session->check('Auth.User.blocked')): ?>

                    <?php if ($session->read('Auth.User.blocked') == 1): ?>

                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading"><?= __d('bechlem_connect_light', 'Oops, unfortunately you are blocked!'); ?></h4>
                            <p><?= __d('bechlem_connect_light', 'This can have various causes and does not mean anything in the first step.'); ?></p>
                            <hr />
                            <p class="mb-0">
                                <?= __d('bechlem_connect_light', 'Please write us an email with the request for verification.'); ?>
                            </p>
                        </div>

                    <?php endif; ?>

                <?php else: ?>

                    <?php $this->Form->setTemplates([
                        'inputContainer' => '{{content}}{{help}}',
                        'inputGroupContainer' => '<div class="input-group mb-3">{{prepend}}{{content}}{{append}}</div>',
                    ]); ?>
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'Users',
                            'action'        => 'login'
                        ],
                        'class' => 'form-login',
                    ]); ?>
                    <?= $this->Form->control('username', [
                        'append'        => $this->Html->icon('user'),
                        'type'          => 'text',
                        'label'         => false,
                        'required'      => true,
                        'placeholder'   => __d('bechlem_connect_light', 'Username'),
                    ]); ?>
                    <?= $this->Form->control('password', [
                        'append'        => $this->Html->icon('lock'),
                        'type'          => 'password',
                        'label'         => false,
                        'required'      => true,
                        'placeholder'   => __d('bechlem_connect_light', 'Password'),
                    ]); ?>
                    <div class="row">
                        <div class="col-12">
                            <?= $this->Html->link(
                                __d('bechlem_connect_light', 'Forgot password'),
                                [
                                    'plugin'        => 'BechlemConnectLight',
                                    'controller'    => 'Users',
                                    'action'        => 'forgot',
                                ],
                                [
                                    'class'         => 'btn btn-' . h($frontendButtonColor),
                                    'escapeTitle'   => false,
                                ]); ?>
                            <?= $this->Form->button(
                                __d('bechlem_connect_light', 'Login'),
                                [
                                    'class'         => 'float-right btn btn-' . h($frontendButtonColor),
                                    'escapeTitle'   => false,
                                ]); ?>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jquery' . DS . 'jquery.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jquery-ui' . DS . 'jquery-ui.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'bootstrap' . DS . 'js' . DS . 'bootstrap.bundle.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jquery-validation' . DS . 'jquery.validate.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jquery-validation' . DS . 'additional-methods.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jqvmap' . DS . 'jquery.vmap.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jqvmap' . DS . 'maps' . DS . 'jquery.vmap.world', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jquery-knob' . DS . 'jquery.knob.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'moment' . DS . 'moment.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'daterangepicker' . DS . 'daterangepicker', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'tempusdominus-bootstrap-4' . DS . 'js' . DS . 'tempusdominus-bootstrap-4.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'summernote' . DS . 'summernote-bs4.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'overlayScrollbars' . DS . 'js' . DS . 'jquery.overlayScrollbars.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'adminlte.min', ['block' => 'scripts']); ?>

    <?= $this->fetch('scripts'); ?>
    <?= $this->fetch('scriptBottom'); ?>

    <?= $this->Html->scriptBlock('$.widget.bridge(\'uibutton\', $.ui.button);'); ?>
    <?= $this->Html->scriptBlock(
        '$(function() {
            $(\'.form-login\').validate({
                rules: {
                    username: {
                        required: true
                    },
                    password: {
                        required: true
                    }
                },
                messages: {
                    username: {
                        required: \'' . __d('bechlem_connect_light', 'Please enter a valid username') . '\'
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
        });'); ?>
</body>
