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
use Cake\Utility\Hash;

// Get session object
$session = $this->getRequest()->getSession();

$backendButtonColor = 'light';
if (Configure::check('BechlemConnectLight.settings.backendButtonColor')):
    $backendButtonColor = Configure::read('BechlemConnectLight.settings.backendButtonColor');
endif;

// Title
$this->assign('title', $this->BechlemConnectLight->readCamel($this->getRequest()->getParam('controller'))
    . ' :: '
    . ucfirst($this->BechlemConnectLight->readCamel($this->getRequest()->getParam('action')))
    . ' :: '
    . h($product->name)
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
    ['title' => __d('bechlem_connect_light', 'View')],
    ['title' => h($product->name)]
]); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= h($product->name); ?> - <?= __d('bechlem_connect_light', 'View'); ?>
                </h3>
                <div class="card-tools">
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'Products',
                            'action'        => 'index',
                        ],
                    ]); ?>
                    <?= $this->Form->control('search', [
                        'type'          => 'text',
                        'label'         => false,
                        'placeholder'   => __d('bechlem_connect_light', 'Search') . '...',
                        'style'         => 'width: 150px;',
                        'append'        => $this->Form->button(
                                __d('bechlem_connect_light', 'Filter'),
                                ['class' => 'btn btn-' . h($backendButtonColor)]
                            )
                            . ' '
                            . $this->Html->link(
                                __d('bechlem_connect_light', 'Reset'),
                                [
                                    'plugin'        => 'BechlemConnectLight',
                                    'controller'    => 'Products',
                                    'action'        => 'index',
                                ],
                                [
                                    'class'     => 'btn btn-' . h($backendButtonColor),
                                    'escape'    => false,
                                ]
                            ),
                    ]); ?>
                    <?= $this->Form->end(); ?>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id'); ?></dt>
                    <dd class="col-sm-9"><?= h($product->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Type'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($product->product_type->title)): ?>
                            <?= $product->has('product_type')?
                            $this->Html->link(h($product->product_type->title), [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'ProductTypes',
                                'action'        => 'view',
                                'id'            => h($product->product_type->id),
                            ]): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Condition'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($product->product_condition->title)): ?>
                            <?= $product->has('product_condition')?
                            $this->Html->link(h($product->product_condition->title), [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'ProductConditions',
                                'action'        => 'view',
                                'id'            => h($product->product_condition->id),
                            ]): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Delivery Time'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($product->product_delivery_time->title)): ?>
                            <?= $product->has('product_delivery_time')?
                            $this->Html->link(h($product->product_delivery_time->title), [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'ProductDeliveryTimes',
                                'action'        => 'view',
                                'id'            => h($product->product_delivery_time->id),
                            ]): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Manufacturer'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($product->product_manufacturer->name)): ?>
                            <?= $product->has('product_manufacturer')?
                            $this->Html->link(h($product->product_manufacturer->name), [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'ProductManufacturers',
                                'action'        => 'view',
                                'id'            => h($product->product_manufacturer->id),
                            ]): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Tax Class'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($product->product_tax_class->title)): ?>
                            <?= $product->has('product_tax_class')?
                            $this->Html->link(h($product->product_tax_class->title), [
                                'plugin'        => 'BechlemConnectLight',
                                'controller'    => 'ProductTaxClasses',
                                'action'        => 'view',
                                'id'            => h($product->product_tax_class->id),
                            ]): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Categories'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($product->product_categories)): ?>
                            <?php foreach($product->product_categories as $category): ?>
                                <?= $this->Html->link(h($category->name), [
                                    'plugin'        => 'BechlemConnectLight',
                                    'controller'    => 'ProductCategories',
                                    'action'        => 'view',
                                    'id'            => h($category->id),
                                ]); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Brands'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($product->product_brands)): ?>
                            <?php foreach($product->product_brands as $brand): ?>
                                <?= $this->Html->link(h($brand->name), [
                                    'plugin'        => 'BechlemConnectLight',
                                    'controller'    => 'ProductBrands',
                                    'action'        => 'view',
                                    'id'            => h($brand->id),
                                ]); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Foreign Key'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->foreign_key)? '-': h($product->foreign_key); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Employee Key'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->employee_key)? '-': h($product->employee_key); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Manufacturer Key'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->manufacturer_key)? '-': h($product->manufacturer_key); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Manufacturer'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->manufacturer)? '-': h($product->manufacturer); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Category Key'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->category_key)? '-': h($product->category_key); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Category'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->category)? '-': h($product->category); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'SKU'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->sku)? '-': h($product->sku); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Manufacturer SKU'); ?> (<?= __d('bechlem_connect_light', 'OEM'); ?>)</dt>
                    <dd class="col-sm-9"><?= empty($product->manufacturer_sku)? '-': h($product->manufacturer_sku); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'EAN'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->ean)? '-': h($product->ean); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->name)? '-': h($product->name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Slug'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->slug)? '-': h($product->slug); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Stock'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->stock)? '0': h($product->stock); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Price'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->price)? $this->Number->currency('0', 'EUR', ['locale' => 'de_DE']): $this->Number->currency(h($product->price), 'EUR', ['locale' => 'de_DE']); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Promote') . ' ' . __d('bechlem_connect_light', 'Start'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->promote_start)? '-': h($product->promote_start->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Promote') . ' ' . __d('bechlem_connect_light', 'End'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->promote_end)? '-': h($product->promote_end->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Promote'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($product->promote)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Promote') . ' ' . __d('bechlem_connect_light', 'Position'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->promote_position)? '-': h($product->promote_position); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Promote New') . ' ' . __d('bechlem_connect_light', 'Start'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->promote_new_start)? '-': h($product->promote_new_start->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Promote New') . ' ' . __d('bechlem_connect_light', 'End'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->promote_new_end)? '-': h($product->promote_new_end->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Promote New'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($product->promote_new)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Promote New') . ' ' . __d('bechlem_connect_light', 'Position'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->promote_new_position)? '-': h($product->promote_new_position); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Status'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($product->status)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'View Counter'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->view_counter)? '0': h($product->view_counter); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->created)? '-': h($product->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= empty($product->modified)? '-': h($product->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
                <hr />
                <?= __d('bechlem_connect_light', 'The {productType} attributes', ['productType' => h($product->product_type->title)]); ?>
                <hr />
                <dl class="row">
                    <?php foreach ($product->product_type->product_type_attributes as $productTypeAttribute): ?>
                        <?php $productProductTypeAttributeValue = Hash::extract(
                            $product->product_product_type_attribute_values,
                            '{n}' . '[product_type_attribute_id = ' . h($productTypeAttribute->id) . ']' . '.' . 'value'); ?>
                        <dt class="col-sm-3"><?= h($productTypeAttribute->title); ?></dt>
                        <dd class="col-sm-9"><?= empty($productProductTypeAttributeValue[0])? '-': $productProductTypeAttributeValue[0]; ?></dd>
                    <?php endforeach; ?>
                </dl>
                <hr/>
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Type Attributes'); ?></dt>
                    <dd>
                        <div class="row">
                            <div class="col-md-6">
                                <ol>
                                    <?php foreach ($product->product_type->product_type_attributes as $productTypeAttribute): ?>
                                        <li><?= $this->Html->link(h($productTypeAttribute->title_alias), [
                                            'plugin'        => 'BechlemConnectLight',
                                            'controller'    => 'ProductTypeAttributes',
                                            'action'        => 'view',
                                            'id'            => h($productTypeAttribute->id),
                                        ]); ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>
                    </dd>
                </dl>
                <hr/>
                <dl>
                    <dd>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="heading">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                                            <?= __d('bechlem_connect_light', 'REST API request') . ':' . ' ' . '/api/products/' . h($product->id) . ' ' . '(' . __d('bechlem_connect_light', 'JSON decoded version') . ')'; ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <pre>
                                            <code class="language-php">
                                                <?php $json = json_encode(['success' => true, 'data' => $product]); ?>
                                                <?= print_r(json_decode($json), true); ?>
                                            </code>
                                        </pre>
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </dd>
                </dl>
                <hr/>
                <?= $this->Html->link(
                    $this->Html->icon('list') . ' ' . __d('bechlem_connect_light', 'Index'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Products',
                        'action'        => 'index',
                    ],
                    [
                        'class'     => 'btn btn-app',
                        'escape'    => false,
                    ]); ?>
                <?= $this->Html->link(
                    $this->Html->icon('edit') . ' ' . __d('bechlem_connect_light', 'Edit'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Products',
                        'action'        => 'edit',
                        'id'            => h($product->id),
                    ],
                    [
                        'class'     => 'btn btn-app',
                        'escape'    => false,
                    ]); ?>
                <?= $this->Form->postLink(
                    $this->Html->icon('copy') . ' ' . __d('bechlem_connect_light', 'Copy'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Products',
                        'action'        => 'copy',
                        'id'            => h($product->id),
                    ],
                    [
                        'class'     => 'btn btn-app',
                        'escape'    => false,
                    ]); ?>
                <?= $this->Form->postLink(
                    $this->Html->icon('trash') . ' ' . __d('bechlem_connect_light', 'Delete'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Products',
                        'action'        => 'delete',
                        'id'            => h($product->id),
                    ],
                    [
                        'confirm' => __d(
                            'bechlem_connect_light',
                            'Are you sure you want to delete "{name}"?',
                            ['name' => h($product->name)]
                        ),
                        'class'     => 'btn btn-app',
                        'escape'    => false,
                    ]); ?>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'prism' . DS . 'prism.min'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'prism' . DS . 'prism.min',
    ['block' => 'scriptBottom']); ?>