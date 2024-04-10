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

$backendBoxColor = 'secondary';
if (Configure::check('BechlemConnectLight.settings.backendBoxColor')):
    $backendBoxColor = Configure::read('BechlemConnectLight.settings.backendBoxColor');
endif;

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
    ['title' => __d('bechlem_connect_light', 'Application documentation')]
]); ?>
<?php if (isset($bechlemConnectDemoData) && ($bechlemConnectDemoData == 1) && !empty($bechlemConnectConfigConnectData->id)): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-danger" role="alert">
            <?= __d('bechlem_connect_light', 'The current Bechlem Connect Config is running on the Bechlem GmbH API Version 1.2 Demo data.'); ?>
            <?= $this->Html->link(
                __d('bechlem_connect_light', 'Please update the default config with your license credentials (username and password).'),
                [
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'BechlemConnectConfigs',
                    'action'        => 'edit',
                    'id'            => h($bechlemConnectConfigConnectData->id),
                ],
                [
                    'class'         => 'alert-link text-light',
                    'title'         => __d('bechlem_connect_light', 'Update default config'),
                    'data-toggle'   => 'tooltip',
                    'escape'        => false,
                ]); ?>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-12" id="applicationDocumentation">
        <h1>
            <?= __d('bechlem_connect_light', 'Application documentation'); ?>
            <?= $this->Html->link(
                $this->Html->tag('i', '', ['class' => 'fas fa-file-pdf']),
                '/admin/application-documentation-pdf',
                [
                    'target'    => '_self',
                    'title'     => __d('bechlem_connect_light', 'Download as PDF'),
                    'escape'    => false,
                ]); ?>
            <?= $this->Html->link(
                $this->Html->tag('i', '', ['class' => 'fas fa-print']),
                '#',
                [
                    'id'        => 'printApplicationDocumentation',
                    'target'    => '_self',
                    'title'     => __d('bechlem_connect_light', 'Print'),
                    'escape'    => false,
                ]); ?>
        </h1>
        <p></p>
    </div>
</div>
<?= $this->Html->scriptBlock(
    '$(function() {
        let printLink = document.getElementById(\'printApplicationDocumentation\');
        let container = document.getElementById(\'applicationDocumentation\');
        printLink.addEventListener(\'click\', event => {
            event.preventDefault();
            window.print();
        }, false);
    });',
    ['block' => 'scriptBottom']); ?>