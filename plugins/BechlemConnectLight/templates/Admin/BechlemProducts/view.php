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

// Title
$this->assign('title', $this->BechlemConnectLight->readCamel($this->getRequest()->getParam('controller'))
    . ' :: '
    . ucfirst($this->BechlemConnectLight->readCamel($this->getRequest()->getParam('action')))
    . ' :: '
    . h($bechlemProduct->bechlem_id)
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
            'controller'    => 'BechlemProducts',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'View')],
    ['title' => h($bechlemProduct->bechlem_id)]
]); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= h($bechlemProduct->bechlem_id); ?> - <?= __d('bechlem_connect_light', 'View'); ?>
                </h3>
                <div class="card-tools">
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'BechlemProducts',
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
                                    'controller'    => 'BechlemProducts',
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
                    <dd class="col-sm-9"><?= h($bechlemProduct->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Bechlem Id'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->bechlem_id)? '-': h($bechlemProduct->bechlem_id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'EAN'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->ean)? '-': h($bechlemProduct->ean); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Manufacturer SKU'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->manufacturer_sku)? '-': h($bechlemProduct->manufacturer_sku); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Your SKU'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->your_sku)? '-': h($bechlemProduct->your_sku); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Manufacturer Id'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->manufacturer_id)? '-': h($bechlemProduct->manufacturer_id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Manufacturer Name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->manufacturer_name)? '-': h($bechlemProduct->manufacturer_name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Product Name With Manufacturer'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->product_name_with_manufacturer)? '-': h($bechlemProduct->product_name_with_manufacturer); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Short Description'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->short_description)? '-': h($bechlemProduct->short_description); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Product Type Id'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->product_type_id)? '-': h($bechlemProduct->product_type_id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Product Type Name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->product_type_name)? '-': h($bechlemProduct->product_type_name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->created)? '-': h($bechlemProduct->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= empty($bechlemProduct->modified)? '-': h($bechlemProduct->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
                <hr/>
                <dl>
                    <dt><?= __d('bechlem_connect_light', 'Image'); ?></dt>
                    <dd><?= empty($bechlemProduct->image)? '-': $this->BechlemConnectLight->bechlemCsdPicture(h($bechlemProduct->image)); ?></dd>
                </dl>
                <?php if (is_array($bechlemProduct->bechlem_product_accessories) && !empty($bechlemProduct->bechlem_product_accessories)): ?>
                    <hr/>
                    <h4><?= __d('bechlem_connect_light', 'Product Accessories'); ?></h4>
                    <table id="referencedProducts" class="table table-hover">
                        <thead>
                        <tr>
                            <th><?= __d('bechlem_connect_light', 'Type'); ?></th>
                            <th><?= __d('bechlem_connect_light', 'Bechlem Id'); ?></th>
                            <th><?= __d('bechlem_connect_light', 'Your SKU'); ?></th>
                            <th><?= __d('bechlem_connect_light', 'Manufacturer SKU'); ?></th>
                            <th><?= __d('bechlem_connect_light', 'EAN'); ?></th>
                            <th><?= __d('bechlem_connect_light', 'Name'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bechlemProduct->bechlem_product_accessories as $bechlemProductAccessory): ?>
                                <?php if (!empty($bechlemProductAccessory->bechlem_product)): ?>
                                    <tr>
                                        <td class="text-success"><?= empty($bechlemProductAccessory->type)? '-': h($bechlemProductAccessory->type); ?></td>
                                        <td class="text-success"><?= empty($bechlemProductAccessory->bechlem_product->bechlem_id)? '-': h($bechlemProductAccessory->bechlem_product->bechlem_id); ?></td>
                                        <td class="text-success"><?= empty($bechlemProductAccessory->bechlem_product->your_sku)? '-': h($bechlemProductAccessory->bechlem_product->your_sku); ?></td>
                                        <td class="text-success"><?= empty($bechlemProductAccessory->bechlem_product->manufacturer_sku)? '-': h($bechlemProductAccessory->bechlem_product->manufacturer_sku); ?></td>
                                        <td class="text-success"><?= empty($bechlemProductAccessory->bechlem_product->ean)? '-': h($bechlemProductAccessory->bechlem_product->ean); ?></td>
                                        <td class="text-success"><?= empty($bechlemProductAccessory->bechlem_product->product_name_with_manufacturer)? '-': h($bechlemProductAccessory->bechlem_product->product_name_with_manufacturer); ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($bechlemProductAccessory->bechlem_printer)): ?>
                                    <?php if (!empty($bechlemProductAccessory->bechlem_product)): ?>
                                        <?php if ($bechlemProductAccessory->bechlem_printer->id_item == $bechlemProductAccessory->bechlem_product->bechlem_id): continue; endif; ?>
                                    <?php endif; ?>
                                    <tr>
                                        <td class="text-info"><?= empty($bechlemProductAccessory->type)? '-': h($bechlemProductAccessory->type); ?></td>
                                        <td class="text-info"><?= empty($bechlemProductAccessory->bechlem_printer->id_item)? '-': h($bechlemProductAccessory->bechlem_printer->id_item); ?></td>
                                        <td class="text-info">-</td>
                                        <td class="text-info"><?= empty($bechlemProductAccessory->bechlem_printer->art_nr)? '-': h($bechlemProductAccessory->bechlem_printer->art_nr); ?></td>
                                        <td class="text-info"><?= empty($bechlemProductAccessory->bechlem_printer->ean)? '-': h($bechlemProductAccessory->bechlem_printer->ean); ?></td>
                                        <td class="text-info"><?= empty($bechlemProductAccessory->bechlem_printer->name)? '-': h($bechlemProductAccessory->bechlem_printer->brand) . ' ' . h($bechlemProductAccessory->bechlem_printer->name) . ' ' . h($bechlemProductAccessory->bechlem_printer->category); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($bechlemProductAccessory->bechlem_supply)): ?>
                                    <?php if (!empty($bechlemProductAccessory->bechlem_product)): ?>
                                        <?php if ($bechlemProductAccessory->bechlem_supply->id_item == $bechlemProductAccessory->bechlem_product->bechlem_id): continue; endif; ?>
                                    <?php endif; ?>
                                    <tr>
                                        <td class="text-primary"><?= empty($bechlemProductAccessory->type)? '-': h($bechlemProductAccessory->type); ?></td>
                                        <td class="text-primary"><?= empty($bechlemProductAccessory->bechlem_supply->id_item)? '-': h($bechlemProductAccessory->bechlem_supply->id_item); ?></td>
                                        <td class="text-primary">-</td>
                                        <td class="text-primary"><?= empty($bechlemProductAccessory->bechlem_supply->art_nr)? '-': h($bechlemProductAccessory->bechlem_supply->art_nr); ?></td>
                                        <td class="text-primary"><?= empty($bechlemProductAccessory->bechlem_supply->ean)? '-': h($bechlemProductAccessory->bechlem_supply->ean); ?></td>
                                        <td class="text-primary"><?= empty($bechlemProductAccessory->bechlem_supply->name)? '-': h($bechlemProductAccessory->bechlem_supply->brand) . ' ' . h($bechlemProductAccessory->bechlem_supply->name) . ' ' . h($bechlemProductAccessory->bechlem_supply->part_nr); ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <hr/>
                <dl>
                    <dd>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="heading">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                                            <?= __d('bechlem_connect_light', 'REST API request') . ':' . ' ' . '/api/bechlem-products/' . h($bechlemProduct->id) . ' ' . '(' . __d('bechlem_connect_light', 'JSON decoded version') . ')'; ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <pre>
                                            <code class="language-php">
                                                <?php $json = json_encode(['success' => true, 'data' => $bechlemProduct]); ?>
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
                        'controller'    => 'BechlemProducts',
                        'action'        => 'index',
                    ],
                    [
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
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-bs4' . DS . 'css' . DS . 'dataTables.bootstrap4.min'); ?>
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-responsive' . DS . 'css' . DS . 'responsive.bootstrap4.min'); ?>
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-buttons' . DS . 'css' . DS . 'buttons.bootstrap4.min'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables' . DS . 'jquery.dataTables.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-bs4' . DS . 'js' . DS . 'dataTables.bootstrap4.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-responsive' . DS . 'js' . DS . 'dataTables.responsive.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-responsive' . DS . 'js' . DS . 'responsive.bootstrap4.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-buttons' . DS . 'js' . DS . 'dataTables.buttons.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-buttons' . DS . 'js' . DS . 'buttons.bootstrap4.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jszip' . DS . 'jszip.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'pdfmake' . DS . 'pdfmake.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'pdfmake' . DS . 'vfs_fonts',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-buttons' . DS . 'js' . DS . 'buttons.html5.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-buttons' . DS . 'js' . DS . 'buttons.print.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'datatables-buttons' . DS . 'js' . DS . 'buttons.colVis.min',
    ['block' => 'scriptBottom']); ?>

<?php if (is_array($bechlemProduct->bechlem_product_accessories) && !empty($bechlemProduct->bechlem_product_accessories)): ?>
    <?= $this->Html->scriptBlock(
        '$(function() {
			// Initialize DataTables
            $(\'#referencedProducts\').DataTable({
                \'responsive\': true,
                \'lengthChange\': true,
                \'autoWidth\': false,
                \'buttons\': [\'copy\', \'csv\', \'excel\', \'pdf\', \'print\', \'colvis\'],
                \'pageLength\': 25,
                \'order\': [[0, \'asc\'],[5, \'asc\']]
            });
        });',
        ['block' => 'scriptBottom']); ?>
<?php endif; ?>