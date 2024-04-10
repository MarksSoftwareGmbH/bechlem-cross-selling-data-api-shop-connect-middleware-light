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

$backendLinkTextColor = 'navy';
if (Configure::check('BechlemConnectLight.settings.backendLinkTextColor')):
    $backendLinkTextColor = Configure::read('BechlemConnectLight.settings.backendLinkTextColor');
endif;

$backendControlSidebar = '0';
if (Configure::check('BechlemConnectLight.settings.backendControlSidebar')):
    $backendControlSidebar = Configure::read('BechlemConnectLight.settings.backendControlSidebar');
endif;
?>
<body class="hold-transition sidebar-mini layout-fixed accent-<?= h($backendLinkTextColor); ?> control-sidebar-slide-open text-sm">
    <div class="wrapper">
        <?= $this->element('flash'); ?>
        <?= $this->element('header'); ?>
        <?= $this->element('sidebar'); ?>
        <?= $this->element('content'); ?>
        <?= $this->element('footer'); ?>
        <?php if ($session->check('Auth.User.id') && ($session->read('Auth.User.role.alias') === 'admin')): ?>
            <?php if ($backendControlSidebar === '1'): ?>
                <?= $this->element('control_sidebar'); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jquery' . DS . 'jquery.min', ['block' => 'scripts']); ?>
    <?= $this->Html->script('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jquery-ui' . DS . 'jquery-ui.min', ['block' => 'scripts']); ?>
    <?= $this->Html->scriptBlock("$.widget.bridge('uibutton', $.ui.button)", ['block' => 'scripts']); ?>
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
</body>