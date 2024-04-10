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
    [
        'title' => $this->BechlemConnectLight->readCamel($this->getRequest()->getParam('controller')),
        'url' => [
            'plugin'        => 'BechlemConnectLight',
            'controller'    => 'RegistrationTypes',
            'action'        => 'index',
        ]
    ],
    ['title' => ucfirst($this->BechlemConnectLight->readCamel($this->getRequest()->getParam('action')))]
]); ?>

<?= $this->Form->create(null, [
    'url' => [
        'plugin'        => 'BechlemConnectLight',
        'controller'    => 'RegistrationTypes',
        'action'        => 'import',
    ],
    'type'  => 'file',
    'class' => 'form-general',
]); ?>
<div class="row">
    <section class="col-lg-8 connectedSortable">
        <div class="card card-<?= h($backendBoxColor); ?>">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $this->Html->icon('upload'); ?> <?= __d('bechlem_connect_light', 'Import'); ?>
                </h3>
            </div>
            <div class="card-body">
                <?= $this->Form->control('delimiter', [
                    'type'      => 'text',
                    'default'   => ';',
                    'readonly',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('enclosure', [
                    'type'      => 'text',
                    'default'   => '"',
                    'readonly',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('file', [
                    'type'      => 'file',
                    'accept'    => 'text/comma-separated-values,text/csv,application/csv',
                    'label'     => __d('bechlem_connect_light', 'Select file'),
                    'required'  => true,
                    'help'      => __d('bechlem_connect_light', 'Please use a valid csv file.')
                ]); ?>
            </div>
        </div>
    </section>
    <section class="col-lg-4 connectedSortable">
        <div class="card card-<?= h($backendBoxColor); ?>">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $this->Html->icon('cog'); ?> <?= __d('bechlem_connect_light', 'Actions'); ?>
                </h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?= $this->Form->checkbox('log', ['id' => 'log', 'class' => 'custom-control-input', 'checked' => false, 'required' => false]); ?>
                        <label class="custom-control-label" for="log"><?= __d('bechlem_connect_light', 'Log'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <?= $this->Form->button(__d('bechlem_connect_light', 'Import'), ['class' => 'btn btn-success']); ?>
                    <?= $this->Html->link(
                        __d('bechlem_connect_light', 'Cancel'),
                        [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'RegistrationTypes',
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

<?= $this->element('please_wait'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'template' . DS . 'admin' . DS . 'registrationTypes' . DS . 'form',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        RegistrationTypes.init();
        $(\'.form-general\').validate({
            rules: {
                delimiter: {
                    required: true
                },
                enclosure: {
                    required: true
                }
            },
            messages: {
                delimiter: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid delimiter') . '\'
                },
                enclosure: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid enclosure') . '\'
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
