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

$backendNavbarColor = 'dark';
if (Configure::check('BechlemConnectLight.settings.backendNavbarColor')):
    $backendNavbarColor = Configure::read('BechlemConnectLight.settings.backendNavbarColor');
endif;

$backendNavbarTextColor = 'white';
if (Configure::check('BechlemConnectLight.settings.backendNavbarTextColor')):
    $backendNavbarTextColor = Configure::read('BechlemConnectLight.settings.backendNavbarTextColor');
endif;

$backendNavbarBackgroundColor = 'navy';
if (Configure::check('BechlemConnectLight.settings.backendNavbarBackgroundColor')):
    $backendNavbarBackgroundColor = Configure::read('BechlemConnectLight.settings.backendNavbarBackgroundColor');
endif;

$backendButtonColor = 'secondary';
if (Configure::check('BechlemConnectLight.settings.backendButtonColor')):
    $backendButtonColor = Configure::read('BechlemConnectLight.settings.backendButtonColor');
endif;

$backendControlSidebar = '0';
if (Configure::check('BechlemConnectLight.settings.backendControlSidebar')):
    $backendControlSidebar = Configure::read('BechlemConnectLight.settings.backendControlSidebar');
endif;
?>
<nav class="main-header navbar navbar-expand navbar-<?= h($backendNavbarColor); ?> text-<?= h($backendNavbarTextColor); ?> bg-<?= h($backendNavbarBackgroundColor); ?> border-bottom-0 text-sm">
    <ul class="navbar-nav">
        <li class="nav-item">
            <?= $this->Html->link(
                $this->Html->icon('bars'),
                'javascript:void(0)',
                [
                    'class'         => 'nav-link',
                    'data-widget'   => 'pushmenu',
                    'role'          => 'button',
                    'escapeTitle'   => false,
                ]); ?>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'Dashboard'),
                [
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Dashboards',
                    'action'        => 'dashboard',
                ],
                ['class' => 'nav-link']); ?>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <?= $this->Html->icon('search'); ?>
            </a>
            <div class="navbar-search-block">
                <?= $this->Form->create(null, [
                    'url' => [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'BechlemProducts',
                        'action'        => 'index',
                    ],
                    'class' => 'form-inline',
                ]); ?>
                <div class="input-group input-group-sm">
                    <?= $this->Form->formGroup('search', [
                        'type'          => 'text',
                        'value'         => $this->getRequest()->getQuery('search'),
                        'label'         => false,
                        'placeholder'   => __d('bechlem_connect_light', 'Search'),
                        'class'         => 'form-control form-control-navbar',
                    ]); ?>
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit">
                            <?= $this->Html->icon('search'); ?>
                        </button>
                        <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                            <?= $this->Html->icon('times'); ?>
                        </button>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </li>
        <?php if ($backendControlSidebar === '1'): ?>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
                    <?= $this->Html->icon('th-large'); ?>
                </a>
            </li>
        <?php endif; ?>
        <li class="nav-item dropdown user-menu">
            <?php if ($session->check('Auth.User.avatar')): ?>
                <?= $this->Html->link(
                    $this->Html->image(
                        $session->read('Auth.User.avatar'),
                        [
                            'alt'   => h($session->read('Auth.User.username')),
                            'class' => 'img-size-50 user-image img-circle elevation-2',
                        ]) .
                    $this->Html->tag('span', $session->read('Auth.User.username'), ['class' => 'd-none d-md-inline']),
                    'javascript:void(0)',
                    [
                        'class'         => 'nav-link dropdown-toggle',
                        'data-toggle'   => 'dropdown',
                        'escapeTitle'   => false,
                    ]); ?>
            <?php else: ?>
                <?= $this->Html->link(
                    $this->Html->image(
                        '/bechlem_connect_light/img/avatars/avatar.jpg',
                        [
                            'alt'   => h($session->read('Auth.User.name')),
                            'class' => 'img-size-50 user-image img-circle elevation-2',
                        ]) .
                    $this->Html->tag('span', $session->read('Auth.User.name'), ['class' => 'd-none d-md-inline']),
                    'javascript:void(0)',
                    [
                        'class'         => 'nav-link dropdown-toggle',
                        'data-toggle'   => 'dropdown',
                        'escapeTitle'   => false,
                    ]); ?>
            <?php endif; ?>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php if ($session->check('Auth.User.avatar')): ?>
                    <li class="user-header bg-<?= h($backendNavbarBackgroundColor); ?>"><?= $this->Html->image(
                        $session->read('Auth.User.avatar'),
                        [
                            'alt'   => h($session->read('Auth.User.username')),
                            'class' => 'img-size-50 img-circle elevation-2',
                        ]); ?><p><?= h($session->read('Auth.User.username')); ?><br /><small><?= h($session->read('Auth.User.email')); ?></small></p></li>
                <?php else: ?>
                    <li class="user-header bg-<?= h($backendNavbarBackgroundColor); ?>"><?= $this->Html->image(
                        '/bechlem_connect_light/img/avatars/avatar.jpg',
                        [
                            'alt'   => h($session->read('Auth.User.name')),
                            'class' => 'img-size-50 img-circle elevation-2',
                        ]); ?><p><?= h($session->read('Auth.User.name')); ?><br /><small><?= h($session->read('Auth.User.email')); ?></small></p></li>
                <?php endif; ?>
                <li class="user-footer">
                    <?= $this->Html->link(
                        $this->Html->icon('user')
                        . ' '
                        . __d('bechlem_connect_light', 'Profile'),
                        [
                            'prefix'        => 'Admin',
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'Users',
                            'action'        => 'profile',
                            'id'            => $session->read('Auth.User.id')
                        ],
                        [
                            'class'         => 'btn btn-' . h($backendButtonColor) . ' btn-flat',
                            'escapeTitle'   => false,
                        ]); ?>
                    <?= $this->Html->link(
                        $this->Html->icon('sign-out-alt')
                        . ' '
                        . __d('bechlem_connect_light', 'Logout'),
                        [
                            'prefix'        => 'Admin',
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'Users',
                            'action'        => 'logout',
                        ],
                        [
                            'class'         => 'btn btn-' . h($backendButtonColor) . ' btn-flat float-right',
                            'escapeTitle'   => false,
                        ]); ?>
                </li>
            </ul>
        </li>
    </ul>
</nav>
