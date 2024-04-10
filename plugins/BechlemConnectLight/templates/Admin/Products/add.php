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
            'controller'    => 'Products',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'Add {title}', ['title' => h($productType->title)])]
]); ?>

<?= $this->Form->create($product, [
    'url' => [
        'plugin'            => 'BechlemConnectLight',
        'controller'        => 'Products',
        'action'            => 'add',
        'productTypeAlias'  => h($productType->alias),
    ],
    'class' => 'form-general',
]); ?>
<div class="row">
    <section class="col-lg-8 connectedSortable">
        <div class="card card-<?= h($backendBoxColor); ?>">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $this->Html->icon('plus'); ?> <?= __d('bechlem_connect_light', 'Add product'); ?>
                </h3>
            </div>
            <div class="card-body">

                <?= $this->Form->control('bechlem_product_key', [
                    'type'  => 'text',
                    'label' => __d('bechlem_connect_light', 'Fetch bechlem product by bechlem id, manufacturer sku or ean'),
                ]); ?>
                <hr />

                <?= $this->Form->control('foreign_key', [
                    'type'      => 'text',
                    'class'     => 'bechlem_id',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('employee_key', [
                    'type'      => 'text',
                    'class'     => 'employee_key',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('manufacturer_key', [
                    'type'      => 'text',
                    'class'     => 'manufacturer_id',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('manufacturer_name', [
                    'type'      => 'text',
                    'class'     => 'manufacturer_name',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('manufacturer_sku', [
                    'type'      => 'text',
                    'class'     => 'manufacturer_sku',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('category_key', [
                    'type'      => 'text',
                    'class'     => 'product_type_id',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('category_name', [
                    'type'      => 'text',
                    'class'     => 'product_type_name',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('sku', [
                    'type'      => 'text',
                    'class'     => 'your_sku',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('ean', [
                    'type'      => 'text',
                    'class'     => 'ean',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('name', [
                    'type'      => 'text',
                    'class'     => 'product_name_with_manufacturer',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('slug', [
                    'type'      => 'text',
                    'class'     => 'slug',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('stock', [
                    'type'      => 'number',
                    'default'   => 0,
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('price', [
                    'type'      => 'text',
                    'default'   => '0.00',
                    'required'  => true,
                ]); ?>

                <hr />
                <?= __d('bechlem_connect_light', 'The {productType} attributes', ['productType' => h($productType->title)]); ?>
                <hr />

                <?php foreach ($productType->product_type_attributes as $key => $productTypeAttribute):

                    echo $this->Form->control('product_product_type_attribute_values' . '.' . h($key) . '.' . 'id');

                    echo $this->Form->control(
                        'product_product_type_attribute_values' . '.' . h($key) . '.' . 'product_type_attribute_id',
                        [
                            'type'  => 'hidden',
                            'value' => h($productTypeAttribute->id),
                        ]);

                    if (empty($productTypeAttribute->product_type_attribute_choices)):

                        if ($productTypeAttribute->empty_value):
                            echo $this->Form->control(
                                'product_product_type_attribute_values' . '.' . h($key) . '.' . 'value',
                                [
                                    'type'          => $this->BechlemConnectLight->inputType($productTypeAttribute->type),
                                    'label'         => h($productTypeAttribute->title),
                                    'placeholder'   => h($productTypeAttribute->title),
                                    'class'         => h($productTypeAttribute->foreign_key),
                                ]);
                        else:
                            echo $this->Form->control(
                                'product_product_type_attribute_values' . '.' . h($key) . '.' . 'value',
                                [
                                    'type'          => $this->BechlemConnectLight->inputType($productTypeAttribute->type),
                                    'label'         => h($productTypeAttribute->title),
                                    'placeholder'   => h($productTypeAttribute->title),
                                    'class'         => h($productTypeAttribute->foreign_key),
                                    'required',
                                ]);
                        endif;

                    else:

                        if ($productTypeAttribute->empty_value):
                            echo $this->Form->control(
                                'product_product_type_attribute_values' . '.' . h($key) . '.' . 'value',
                                [
                                    'type'          => $this->BechlemConnectLight->inputType($productTypeAttribute->type),
                                    'options'       => !empty($productTypeAttribute->product_type_attribute_choices)? $this->BechlemConnectLight->inputOptions($productTypeAttribute->product_type_attribute_choices): '',
                                    'class'         => 'select2',
                                    'style'         => 'width: 100%',
                                    'empty'         => true,
                                    'label'         => h($productTypeAttribute->title),
                                    'placeholder'   => h($productTypeAttribute->title),
                                ]);
                        else:
                            echo $this->Form->control(
                                'product_product_type_attribute_values' . '.' . h($key) . '.' . 'value',
                                [
                                    'type'          => $this->BechlemConnectLight->inputType($productTypeAttribute->type),
                                    'options'       => !empty($productTypeAttribute->product_type_attribute_choices)? $this->BechlemConnectLight->inputOptions($productTypeAttribute->product_type_attribute_choices) : '',
                                    'class'         => 'select2',
                                    'style'         => 'width: 100%',
                                    'empty'         => true,
                                    'label'         => h($productTypeAttribute->title),
                                    'placeholder'   => h($productTypeAttribute->title),
                                    'required',
                                ]);
                        endif;

                    endif;

                endforeach;
                ?>

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
                <?= $this->Form->control('product_type_id', [
                    'type'  => 'hidden',
                    'value' => h($productType->id),
                ]); ?>
                <?= $this->Form->control('product_condition_id', [
                    'type'      => 'select',
                    'label'     => __d('bechlem_connect_light', 'Condition'),
                    'options'   => !empty($productConditions)? $productConditions: [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => true,
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('product_delivery_time_id', [
                    'type'      => 'select',
                    'label'     => __d('bechlem_connect_light', 'Delivery time'),
                    'options'   => !empty($productDeliveryTimes)? $productDeliveryTimes: [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => true,
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('product_tax_class_id', [
                    'type' => 'select',
                    'label' => __d('bechlem_connect_light', 'Tax class'),
                    'options' => !empty($productTaxClasses)? $productTaxClasses: [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty' => true,
                    'required' => true,
                ]); ?>
                <?= $this->Form->control('product_manufacturer_id', [
                    'type'      => 'select',
                    'label'     => __d('bechlem_connect_light', 'Manufacturer'),
                    'options'   => !empty($productManufacturers)? $productManufacturers: [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => true,
                    'required'  => true,
                ]); ?>
                <div class="form-group">
                <?= $this->Form->control('product_brands._ids', [
                    'type'      => 'select',
                    'multiple'  => 'select',
                    'options'   => !empty($productBrands)? $productBrands: [],
                    'label'     => __d('bechlem_connect_light', 'Brands'),
                    'class'     => 'duallistbox',
                ]); ?>
                </div>
                <div class="form-group">
                <?= $this->Form->control('product_categories._ids', [
                    'type'      => 'select',
                    'multiple'  => 'select',
                    'options'   => !empty($productCategories)? $productCategories: [],
                    'label'     => __d('bechlem_connect_light', 'Categories'),
                    'class'     => 'duallistbox',
                ]); ?>
                </div>
                <?= $this->Form->control('promote_start', [
                    'type'      => 'text',
                    'label'     => __d('bechlem_connect_light', 'Promote start'),
                    'class'     => 'datetimepicker',
                    'format'    => 'd.m.Y H:i',
                    'default'   => date('d.m.Y H:i'),
                ]); ?>
                <?= $this->Form->control('promote_end', [
                    'type'      => 'text',
                    'label'     => __d('bechlem_connect_light', 'Promote end'),
                    'class'     => 'datetimepicker',
                    'format'    => 'd.m.Y H:i',
                ]); ?>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?= $this->Form->checkbox('promote', ['id' => 'promote', 'class' => 'custom-control-input', 'checked' => false, 'required' => false]); ?>
                        <label class="custom-control-label" for="promote"><?= __d('bechlem_connect_light', 'Promote'); ?></label>
                    </div>
                </div>
                <?= $this->Form->control('promote_position', [
                    'type'      => 'number',
                    'default'   => 0,
                ]); ?>
                <?= $this->Form->control('promote_new_start', [
                    'type'      => 'text',
                    'label'     => __d('bechlem_connect_light', 'Promote new start'),
                    'class'     => 'datetimepicker',
                    'format'    => 'd.m.Y H:i',
                    'default'   => date('d.m.Y H:i'),
                ]); ?>
                <?= $this->Form->control('promote_new_end', [
                    'type'      => 'text',
                    'label'     => __d('bechlem_connect_light', 'Promote new end'),
                    'class'     => 'datetimepicker',
                    'format'    => 'd.m.Y H:i',
                ]); ?>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?= $this->Form->checkbox('promote_new', ['id' => 'promoteNew', 'class' => 'custom-control-input', 'checked' => false, 'required' => false]); ?>
                        <label class="custom-control-label" for="promoteNew"><?= __d('bechlem_connect_light', 'Promote new'); ?></label>
                    </div>
                </div>
                <?= $this->Form->control('promote_new_position', [
                    'type'      => 'number',
                    'default'   => 0,
                ]); ?>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?= $this->Form->checkbox('status', ['id' => 'status', 'class' => 'custom-control-input', 'checked' => false, 'required' => false]); ?>
                        <label class="custom-control-label" for="status"><?= __d('bechlem_connect_light', 'Status'); ?></label>
                    </div>
                </div>
                <?= $this->Form->control('view_counter', [
                    'type'      => 'hidden',
                    'value'     => 0,
                ]); ?>
                <div class="form-group">
                    <?= $this->Form->button(__d('bechlem_connect_light', 'Submit'), ['class' => 'btn btn-success']); ?>
                    <?= $this->Html->link(
                        __d('bechlem_connect_light', 'Cancel'),
                        [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'Products',
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
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'bootstrap4-duallistbox' . DS . 'bootstrap-duallistbox.min'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'bootstrap4-duallistbox' . DS . 'jquery.bootstrap-duallistbox.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datetimepicker' . DS . 'jquery.datetimepicker'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datetimepicker' . DS . 'build' . DS . 'jquery.datetimepicker.full.min',
    ['block' => 'scriptBottom']); ?>

<?php $ckeditorScript = ''; ?>
<?php foreach ($productType->product_type_attributes as $key => $productTypeAttribute): ?>
    <?php $textareaId = 'product-product-type-attribute-values' . '-' . h($key) . '-' . 'value'; ?>
    <?php if ($productTypeAttribute->wysiwyg): ?>
        <?php $ckeditorScript .= '
            $(\'#' . h($textareaId) . '\').summernote();
            $(\'.form-general\').submit(function(event) {
                $(\'#' . h($textareaId) . '\').summernote(\'destroy\');
            });' ?>
    <?php endif; ?>
<?php endforeach; ?>

<?= $this->Html->scriptBlock(
    '$(function() {
        ' . $ckeditorScript . '
        // Initialize select2
        $(\'.select2\').select2();
        // Initialize duallistbox
        $(\'.duallistbox\').bootstrapDualListbox();
        // Initialize datetimepicker
        $(\'.datetimepicker\').datetimepicker({
            format:\'d.m.Y H:i\',
            lang:\'en\'
        });
        $(\'.form-general\').validate({
            rules: {
                manufacturer_key: {
                    required: true
                },
                manufacturer: {
                    required: true
                },
                category_key: {
                    required: true
                },
                category: {
                    required: true
                },
                sku: {
                    required: true
                },
                manufacturer_sku: {
                    required: true
                },
                name: {
                    required: true
                },
                slug: {
                    required: true
                },
                stock: {
                    required: true
                },
                price: {
                    required: true
                }
            },
            messages: {
                manufacturer_key: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid manufacturer key') . '\'
                },
                manufacturer: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid manufacturer') . '\'
                },
                category_key: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid category key') . '\'
                },
                category: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid category') . '\'
                },
                sku: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid sku') . '\'
                },
                manufacturer_sku: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid manufacturer sku') . '\'
                },
                name: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid name') . '\'
                },
                slug: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid slug') . '\'
                },
                stock: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid stock') . '\'
                },
                price: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid price') . '\'
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

<?php $ajaxAttributeValues = ''; ?>
<?php $ajaxAttributes = [
    'bechlem_id',
    'employee_key',
    'manufacturer_id',
    'manufacturer_name',
    'manufacturer_sku',
    'product_type_id',
    'product_type_name',
    'your_sku',
    'ean',
    'product_name_with_manufacturer',
    'slug',
    'stock',
    'price',
]; ?>
<?php foreach ($ajaxAttributes as $ajaxAttribute): ?>
    <?php $ajaxAttributeValues .= "$('." . h($ajaxAttribute) . "').val(data.product." . h($ajaxAttribute) . ");\n"; ?>
<?php endforeach; ?>
<?php foreach ($productType->product_type_attributes as $productTypeAttribute): ?>
    <?php if (isset($productTypeAttribute->foreign_key) && !empty($productTypeAttribute->foreign_key)): ?>
        <?php if (!in_array($productTypeAttribute->foreign_key, $ajaxAttributes)): ?>
            <?php array_push($ajaxAttributes, $productTypeAttribute->foreign_key); ?>
            <?php $ajaxAttributeValues .= "$('." . h($productTypeAttribute->foreign_key) . "').val(data.product." . h($productTypeAttribute->foreign_key) . ");\n"; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>

<?= $this->Html->scriptBlock(
    '$(function() {
        // Load data by selected id
        $(\'#bechlem-product-key\').on(\'input propertychange paste\', function() {
            $.ajax({
                url: \'' . $this->Url->build([
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'BechlemProducts',
                    'action'        => 'loadData',
                ]) . '\',
                cache: false,
                type: \'GET\',
                data: { key: $(this).val() },
                dataType: \'json\',
                success: function(data) {
                    if (typeof data.product !== \'undefined\') {
                        // Get data from the ajax service
                        ' . $ajaxAttributeValues . '
                    }
                }
            });
        });
    });',
    ['block' => 'scriptBottom']); ?>
