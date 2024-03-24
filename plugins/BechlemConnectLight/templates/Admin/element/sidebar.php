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

$backendSidebarColor = 'dark';
if (Configure::check('BechlemConnectLight.settings.backendSidebarColor')):
    $backendSidebarColor = Configure::read('BechlemConnectLight.settings.backendSidebarColor');
endif;

$backendSidebarTextColor = 'white';
if (Configure::check('BechlemConnectLight.settings.backendSidebarTextColor')):
    $backendSidebarTextColor = Configure::read('BechlemConnectLight.settings.backendSidebarTextColor');
endif;

$backendSidebarBackgroundColor = 'navy';
if (Configure::check('BechlemConnectLight.settings.backendSidebarBackgroundColor')):
    $backendSidebarBackgroundColor = Configure::read('BechlemConnectLight.settings.backendSidebarBackgroundColor');
endif;
?>
<aside class="main-sidebar sidebar-<?= h($backendSidebarColor); ?>-<?= h($backendSidebarTextColor); ?> elevation-4">
    <?= $this->Html->link(
        $this->Html->image(
            'logo_icon.png',
            [
                'alt'   => 'BECHLEM CONNECT LIGHT',
                'class' => 'brand-image img-circle elevation-3',
                'style' => 'opacity: .8',
            ]
        )
        . $this->Html->tag('span', __d('bechlem_connect_light', 'BECHLEM CONNECT LIGHT'), ['class' => 'brand-text font-weight-light']),
        '/',
        [
            'class'         => 'brand-link bg-' . h($backendSidebarBackgroundColor),
            'escapeTitle'   => false,
        ]); ?>
    <div class="sidebar">
        <nav class="mt-2">
            <?= $this->Menu->menu($this->getRequest()->getAttribute('params')); ?>
        </nav>
    </div>
</aside>
