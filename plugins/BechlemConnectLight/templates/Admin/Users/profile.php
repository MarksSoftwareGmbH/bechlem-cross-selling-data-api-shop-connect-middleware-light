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
    . h($user->name)
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
    ['title' => __d('bechlem_connect_light', 'Profile')],
    ['title' => h($user->name)]
]); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= h($user->name); ?> - <?= __d('bechlem_connect_light', 'Profile'); ?>
                </h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id'); ?></dt>
                    <dd class="col-sm-9"><?= h($user->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Role'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($user->role->title)): ?>
                            <?= $user->has('role')? h($user->role->title): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Locale'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($user->locale->name)): ?>
                            <?= $user->has('locale')? h($user->locale->name): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Foreign key'); ?></dt>
                    <dd class="col-sm-9"><?= empty($user->foreign_key)? '-': h($user->foreign_key); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Username'); ?></dt>
                    <dd class="col-sm-9"><?= h($user->username); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Name'); ?></dt>
                    <dd class="col-sm-9"><?= h($user->name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Email'); ?></dt>
                    <dd class="col-sm-9"><?= empty($user->email)? '-': $this->Html->link($user->email, 'mailto:' . $user->email); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Status'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($user->status)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Activation date'); ?></dt>
                    <dd class="col-sm-9"><?= empty($user->activation_date)? '-': h($user->activation_date->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Last login'); ?></dt>
                    <dd class="col-sm-9"><?= empty($user->last_login)? '-': h($user->last_login->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= empty($user->created)? '-': h($user->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= empty($user->modified)? '-': h($user->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
