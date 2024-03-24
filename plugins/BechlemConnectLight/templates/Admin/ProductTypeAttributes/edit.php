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
    . h($productTypeAttribute->title)
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
            'controller'    => 'ProductTypeAttributes',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'Edit Product Type Attribute')],
    ['title' => h($productTypeAttribute->title)]
]); ?>

<?= $this->Form->create($productTypeAttribute, ['class' => 'form-general']); ?>
<div class="row">
    <section class="col-lg-8 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $this->Html->icon('edit'); ?> <?= __d('bechlem_connect_light', 'Edit Product Type Attribute'); ?>
                </h3>
            </div>
            <div class="card-body">
                <?= $this->Form->control('foreign_key', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('title', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('alias', [
                    'type'      => 'text',
                    'class'     => 'slug',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('description', [
                    'type'      => 'textarea',
                    'class'     => 'description',
                    'required'  => false,
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
                <?= $this->Form->control('type', [
                    'type'      => 'select',
                    'options'   => !empty($this->BechlemConnectLight->inputTypesList())? $this->BechlemConnectLight->inputTypesList(): [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('product_types._ids', [
                    'type'      => 'select',
                    'multiple'  => 'checkbox',
                    'options'   => !empty($productTypes)? $productTypes: [],
                    'label'     => __d('bechlem_connect_light', 'Types'),
                ]); ?>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?php $emptyValue = $productTypeAttribute->empty_value? true: false; ?>
                        <?= $this->Form->checkbox('empty_value', ['id' => 'emptyValue', 'class' => 'custom-control-input', 'checked' => $emptyValue, 'required' => false]); ?>
                        <label class="custom-control-label" for="emptyValue"><?= __d('bechlem_connect_light', 'Leave attribute empty?'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?php $wysiwyg = $productTypeAttribute->wysiwyg? true: false; ?>
                        <?= $this->Form->checkbox('wysiwyg', ['id' => 'wysiwyg', 'class' => 'custom-control-input', 'checked' => $wysiwyg, 'required' => false]); ?>
                        <label class="custom-control-label" for="wysiwyg"><?= __d('bechlem_connect_light', 'Activate WYSIWYG editor?'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <?= $this->Form->button(__d('bechlem_connect_light', 'Submit'), ['class' => 'btn btn-success']); ?>
                    <?= $this->Html->link(
                        __d('bechlem_connect_light', 'Cancel'),
                        [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'ProductTypeAttributes',
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

<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'select2' . DS . 'css' . DS . 'select2.min'); ?>
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'bechlem_connect_light.select2'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'select2' . DS . 'js' . DS . 'select2.full.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'slug' . DS . 'jquery.slug',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'template' . DS . 'admin' . DS . 'productTypeAttributes' . DS . 'form',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        ProductTypeAttributes.init();
        // Initialize select2
        $(\'.select2\').select2();
        // Initialize summernote
        $(\'.description\').summernote();
        $(\'.form-general\').submit(function(event) {
            $(\'.description\').summernote(\'destroy\');
        });
        $(\'.form-general\').validate({
            rules: {
                title: {
                    required: true
                },
                alias: {
                    required: true
                }
            },
            messages: {
                title: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid title') . '\'
                },
                alias: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid alias') . '\'
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
