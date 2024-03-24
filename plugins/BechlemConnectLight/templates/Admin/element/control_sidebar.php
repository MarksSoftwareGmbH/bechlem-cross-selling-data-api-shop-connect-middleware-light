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

$backendLinkTextColor = 'navy';
if (Configure::check('BechlemConnectLight.settings.backendLinkTextColor')):
    $backendLinkTextColor = Configure::read('BechlemConnectLight.settings.backendLinkTextColor');
endif;

$backendButtonColor = 'secondary';
if (Configure::check('BechlemConnectLight.settings.backendButtonColor')):
    $backendButtonColor = Configure::read('BechlemConnectLight.settings.backendButtonColor');
endif;

$backendBoxColor = 'secondary';
if (Configure::check('BechlemConnectLight.settings.backendBoxColor')):
    $backendBoxColor = Configure::read('BechlemConnectLight.settings.backendBoxColor');
endif;

?>
<aside class="control-sidebar control-sidebar-<?= h($backendSidebarColor); ?> customize-the-backend">
    <div class="p-3 control-sidebar-content">
        <h5><?= __d('bechlem_connect_light', 'Customize the backend'); ?></h5>
        <hr class="mb-2">

        <?= $this->Form->create(null, [
            'type'  => 'post',
            'url'   => [
                'controller'    => 'Settings',
                'action'        => 'edit',
                'id'            => 3,
            ],
            'class' => 'form-general'
        ]); ?>
            <?php $backendNavbarColorOptions = [
                'white'     => __d('bechlem_connect_light', 'White'),
                'warning'   => __d('bechlem_connect_light', 'Warning'),
                'orange'    => __d('bechlem_connect_light', 'Orange'),
                'dark'      => __d('bechlem_connect_light', 'Dark'),
                'light'     => __d('bechlem_connect_light', 'Light'),
            ]; ?>
            <?= $this->Form->control('value', [
                'type'      => 'select',
                'label'     => __d('bechlem_connect_light', 'Navbar color'),
                'default'   => h($backendNavbarColor),
                'options'   => h($backendNavbarColorOptions),
                'class'     => 'custom-select mb-3 border-0 text-' . h($backendButtonColor) . ' bg-' . h($backendBoxColor),
                'style'     => 'width: 100%',
                'empty'     => false,
                'required'  => true,
                'onchange'  => 'this.form.submit();',
            ]); ?>
        <?= $this->Form->end(); ?>

        <?= $this->Form->create(null, [
            'type'  => 'post',
            'url'   => [
                'controller'    => 'Settings',
                'action'        => 'edit',
                'id'            => 4,
            ],
            'class' => 'form-general'
        ]); ?>
            <?php $backendNavbarTextColorOptions = [
                'white'     => __d('bechlem_connect_light', 'White'),
                'primary'   => __d('bechlem_connect_light', 'Primary'),
                'secondary' => __d('bechlem_connect_light', 'Secondary'),
                'info'      => __d('bechlem_connect_light', 'Info'),
                'success'   => __d('bechlem_connect_light', 'Success'),
                'danger'    => __d('bechlem_connect_light', 'Danger'),
                'indigo'    => __d('bechlem_connect_light', 'Indigo'),
                'purple'    => __d('bechlem_connect_light', 'Purple'),
                'pink'      => __d('bechlem_connect_light', 'Pink'),
                'navy'      => __d('bechlem_connect_light', 'Navy'),
                'lightblue' => __d('bechlem_connect_light', 'Lightblue'),
                'teal'      => __d('bechlem_connect_light', 'Teal'),
                'cyan'      => __d('bechlem_connect_light', 'Cyan'),
                'gray'      => __d('bechlem_connect_light', 'Gray'),
                'gray-dark' => __d('bechlem_connect_light', 'Gray-Dark'),
                'dark'      => __d('bechlem_connect_light', 'Dark'),
                'light'     => __d('bechlem_connect_light', 'Light'),
            ]; ?>
            <?= $this->Form->control('value', [
                'type'      => 'select',
                'label'     => __d('bechlem_connect_light', 'Navbar text color'),
                'default'   => h($backendNavbarTextColor),
                'options'   => h($backendNavbarTextColorOptions),
                'class'     => 'custom-select mb-3 border-0 text-' . h($backendButtonColor) . ' bg-' . h($backendBoxColor),
                'style'     => 'width: 100%',
                'empty'     => false,
                'required'  => true,
                'onchange'  => 'this.form.submit();',
            ]); ?>
        <?= $this->Form->end(); ?>

        <?= $this->Form->create(null, [
            'type' => 'post',
            'url' => [
                'controller'    => 'Settings',
                'action'        => 'edit',
                'id'            => 5,
            ],
            'class' => 'form-general'
        ]); ?>
            <?php $backendNavbarBackgroundColorOptions = [
                'primary'   => __d('bechlem_connect_light', 'Primary'),
                'secondary' => __d('bechlem_connect_light', 'Secondary'),
                'info'      => __d('bechlem_connect_light', 'Info'),
                'success'   => __d('bechlem_connect_light', 'Success'),
                'warning'   => __d('bechlem_connect_light', 'Warning'),
                'danger'    => __d('bechlem_connect_light', 'Danger'),
                'indigo'    => __d('bechlem_connect_light', 'Indigo'),
                'purple'    => __d('bechlem_connect_light', 'Purple'),
                'pink'      => __d('bechlem_connect_light', 'Pink'),
                'navy'      => __d('bechlem_connect_light', 'Navy'),
                'lightblue' => __d('bechlem_connect_light', 'Lightblue'),
                'fuchsia'   => __d('bechlem_connect_light', 'Fuchsia'),
                'teal'      => __d('bechlem_connect_light', 'Teal'),
                'olive'     => __d('bechlem_connect_light', 'Olive'),
                'maroon'    => __d('bechlem_connect_light', 'Maroon'),
                'orange'    => __d('bechlem_connect_light', 'Orange'),
                'lime'      => __d('bechlem_connect_light', 'Lime'),
                'cyan'      => __d('bechlem_connect_light', 'Cyan'),
                'gray'      => __d('bechlem_connect_light', 'Gray'),
                'gray-dark' => __d('bechlem_connect_light', 'Gray-Dark'),
                'dark'      => __d('bechlem_connect_light', 'Dark'),
            ]; ?>
            <?= $this->Form->control('value', [
                'type'      => 'select',
                'label'     => __d('bechlem_connect_light', 'Navbar background color'),
                'default'   => h($backendNavbarBackgroundColor),
                'options'   => h($backendNavbarBackgroundColorOptions),
                'class'     => 'custom-select mb-3 border-0 text-' . h($backendButtonColor) . ' bg-' . h($backendBoxColor),
                'style'     => 'width: 100%',
                'empty'     => false,
                'required'  => true,
                'onchange'  => 'this.form.submit();',
            ]); ?>
        <?= $this->Form->end(); ?>

        <?= $this->Form->create(null, [
            'type'  => 'post',
            'url'   => [
                'controller'    => 'Settings',
                'action'        => 'edit',
                'id'            => 6,
            ],
            'class' => 'form-general'
        ]); ?>
            <?php $backendSidebarColorOptions = [
                'dark'      => __d('bechlem_connect_light', 'Dark'),
                'light'     => __d('bechlem_connect_light', 'Light'),
            ]; ?>
            <?= $this->Form->control('value', [
                'type'      => 'select',
                'label'     => __d('bechlem_connect_light', 'Sidebar color'),
                'default'   => h($backendSidebarColor),
                'options'   => h($backendSidebarColorOptions),
                'class'     => 'custom-select mb-3 border-0 text-' . h($backendButtonColor) . ' bg-' . h($backendBoxColor),
                'style'     => 'width: 100%',
                'empty'     => false,
                'required'  => true,
                'onchange'  => 'this.form.submit();',
            ]); ?>
        <?= $this->Form->end(); ?>

        <?= $this->Form->create(null, [
            'type'  => 'post',
            'url'   => [
                'controller'    => 'Settings',
                'action'        => 'edit',
                'id'            => 7,
            ],
            'class' => 'form-general'
        ]); ?>
            <?php $backendSidebarTextColorOptions = [
                'primary'   => __d('bechlem_connect_light', 'Primary'),
                'info'      => __d('bechlem_connect_light', 'Info'),
                'success'   => __d('bechlem_connect_light', 'Success'),
                'warning'   => __d('bechlem_connect_light', 'Warning'),
                'danger'    => __d('bechlem_connect_light', 'Danger'),
                'indigo'    => __d('bechlem_connect_light', 'Indigo'),
                'lightblue' => __d('bechlem_connect_light', 'Lightblue'),
                'navy'      => __d('bechlem_connect_light', 'Navy'),
                'purple'    => __d('bechlem_connect_light', 'Purple'),
                'fuchsia'   => __d('bechlem_connect_light', 'Fuchsia'),
                'pink'      => __d('bechlem_connect_light', 'Pink'),
                'maroon'    => __d('bechlem_connect_light', 'Maroon'),
                'orange'    => __d('bechlem_connect_light', 'Orange'),
                'lime'      => __d('bechlem_connect_light', 'Lime'),
                'teal'      => __d('bechlem_connect_light', 'Teal'),
                'olive'     => __d('bechlem_connect_light', 'Olive'),
            ]; ?>
            <?= $this->Form->control('value', [
                'type'      => 'select',
                'label'     => __d('bechlem_connect_light', 'Sidebar text color'),
                'default'   => h($backendSidebarTextColor),
                'options'   => h($backendSidebarTextColorOptions),
                'class'     => 'custom-select mb-3 border-0 text-' . h($backendButtonColor) . ' bg-' . h($backendBoxColor),
                'style'     => 'width: 100%',
                'empty'     => false,
                'required'  => true,
                'onchange'  => 'this.form.submit();',
            ]); ?>
        <?= $this->Form->end(); ?>

        <?= $this->Form->create(null, [
            'type'  => 'post',
            'url'   => [
                'controller'    => 'Settings',
                'action'        => 'edit',
                'id'            => 8,
            ],
            'class' => 'form-general'
        ]); ?>
            <?php $backendSidebarBackgroundColorOptions = [
                'primary'   => __d('bechlem_connect_light', 'Primary'),
                'info'      => __d('bechlem_connect_light', 'Info'),
                'success'   => __d('bechlem_connect_light', 'Success'),
                'warning'   => __d('bechlem_connect_light', 'Warning'),
                'danger'    => __d('bechlem_connect_light', 'Danger'),
                'indigo'    => __d('bechlem_connect_light', 'Indigo'),
                'lightblue' => __d('bechlem_connect_light', 'Lightblue'),
                'navy'      => __d('bechlem_connect_light', 'Navy'),
                'purple'    => __d('bechlem_connect_light', 'Purple'),
                'fuchsia'   => __d('bechlem_connect_light', 'Fuchsia'),
                'pink'      => __d('bechlem_connect_light', 'Pink'),
                'maroon'    => __d('bechlem_connect_light', 'Maroon'),
                'orange'    => __d('bechlem_connect_light', 'Orange'),
                'lime'      => __d('bechlem_connect_light', 'Lime'),
                'teal'      => __d('bechlem_connect_light', 'Teal'),
                'olive'     => __d('bechlem_connect_light', 'Olive'),
            ]; ?>
            <?= $this->Form->control('value', [
                'type'      => 'select',
                'label'     => __d('bechlem_connect_light', 'Sidebar background color'),
                'default'   => h($backendSidebarBackgroundColor),
                'options'   => h($backendSidebarBackgroundColorOptions),
                'class'     => 'custom-select mb-3 border-0 text-' . h($backendButtonColor) . ' bg-' . h($backendBoxColor),
                'style'     => 'width: 100%',
                'empty'     => false,
                'required'  => true,
                'onchange'  => 'this.form.submit();',
            ]); ?>
        <?= $this->Form->end(); ?>

        <?= $this->Form->create(null, [
            'type'  => 'post',
            'url'   => [
                'controller'    => 'Settings',
                'action'        => 'edit',
                'id'            => 9,
            ],
            'class' => 'form-general'
        ]); ?>
            <?php $backendLinkTextColorOptions = [
                'primary'   => __d('bechlem_connect_light', 'Primary'),
                'info'      => __d('bechlem_connect_light', 'Info'),
                'success'   => __d('bechlem_connect_light', 'Success'),
                'warning'   => __d('bechlem_connect_light', 'Warning'),
                'danger'    => __d('bechlem_connect_light', 'Danger'),
                'indigo'    => __d('bechlem_connect_light', 'Indigo'),
                'lightblue' => __d('bechlem_connect_light', 'Lightblue'),
                'navy'      => __d('bechlem_connect_light', 'Navy'),
                'purple'    => __d('bechlem_connect_light', 'Purple'),
                'fuchsia'   => __d('bechlem_connect_light', 'Fuchsia'),
                'pink'      => __d('bechlem_connect_light', 'Pink'),
                'maroon'    => __d('bechlem_connect_light', 'Maroon'),
                'orange'    => __d('bechlem_connect_light', 'Orange'),
                'lime'      => __d('bechlem_connect_light', 'Lime'),
                'teal'      => __d('bechlem_connect_light', 'Teal'),
                'olive'     => __d('bechlem_connect_light', 'Olive'),
            ]; ?>
            <?= $this->Form->control('value', [
                'type'      => 'select',
                'label'     => __d('bechlem_connect_light', 'Link text color'),
                'default'   => h($backendLinkTextColor),
                'options'   => h($backendLinkTextColorOptions),
                'class'     => 'custom-select mb-3 border-0 text-' . h($backendButtonColor) . ' bg-' . h($backendBoxColor),
                'style'     => 'width: 100%',
                'empty'     => false,
                'required'  => true,
                'onchange'  => 'this.form.submit();',
            ]); ?>
        <?= $this->Form->end(); ?>

        <?= $this->Form->create(null, [
            'type'  => 'post',
            'url'   => [
                'controller'    => 'Settings',
                'action'        => 'edit',
                'id'            => 10,
            ],
            'class' => 'form-general'
        ]); ?>
            <?php $backendButtonColorOptions = [
                'dark'                  => __d('bechlem_connect_light', 'Dark'),
                'light'                 => __d('bechlem_connect_light', 'Light'),
                'primary'               => __d('bechlem_connect_light', 'Primary'),
                'secondary'             => __d('bechlem_connect_light', 'Secondary'),
                'info'                  => __d('bechlem_connect_light', 'Info'),
                'success'               => __d('bechlem_connect_light', 'Success'),
                'warning'               => __d('bechlem_connect_light', 'Warning'),
                'danger'                => __d('bechlem_connect_light', 'Danger'),
                'outline-dark'          => __d('bechlem_connect_light', 'Outline Dark'),
                'outline-light'         => __d('bechlem_connect_light', 'Outline Light'),
                'outline-primary'       => __d('bechlem_connect_light', 'Outline Primary'),
                'outline-secondary'     => __d('bechlem_connect_light', 'Outline Secondary'),
                'outline-info'          => __d('bechlem_connect_light', 'Outline Info'),
                'outline-success'       => __d('bechlem_connect_light', 'Outline Success'),
                'outline-warning'       => __d('bechlem_connect_light', 'Outline Warning'),
                'outline-danger'        => __d('bechlem_connect_light', 'Outline Danger'),
            ]; ?>
            <?= $this->Form->control('value', [
                'type'      => 'select',
                'label'     => __d('bechlem_connect_light', 'Button color'),
                'default'   => h($backendButtonColor),
                'options'   => h($backendButtonColorOptions),
                'class'     => 'custom-select mb-3 border-0 text-' . h($backendButtonColor) . ' bg-' . h($backendBoxColor),
                'style'     => 'width: 100%',
                'empty'     => false,
                'required'  => true,
                'onchange'  => 'this.form.submit();',
            ]); ?>
        <?= $this->Form->end(); ?>

        <?= $this->Form->create(null, [
            'type'  => 'post',
            'url'   => [
                'controller'    => 'Settings',
                'action'        => 'edit',
                'id'            => 11,
            ],
            'class' => 'form-general'
        ]); ?>
            <?php $backendBoxColorOptions = [
                'primary'   => __d('bechlem_connect_light', 'Primary'),
                'secondary' => __d('bechlem_connect_light', 'Secondary'),
                'info'      => __d('bechlem_connect_light', 'Info'),
                'success'   => __d('bechlem_connect_light', 'Success'),
                'warning'   => __d('bechlem_connect_light', 'Warning'),
                'danger'    => __d('bechlem_connect_light', 'Danger'),
                'indigo'    => __d('bechlem_connect_light', 'Indigo'),
                'purple'    => __d('bechlem_connect_light', 'Purple'),
                'pink'      => __d('bechlem_connect_light', 'Pink'),
                'navy'      => __d('bechlem_connect_light', 'Navy'),
                'lightblue' => __d('bechlem_connect_light', 'Lightblue'),
                'fuchsia'   => __d('bechlem_connect_light', 'Fuchsia'),
                'teal'      => __d('bechlem_connect_light', 'Teal'),
                'olive'     => __d('bechlem_connect_light', 'Olive'),
                'maroon'    => __d('bechlem_connect_light', 'Maroon'),
                'orange'    => __d('bechlem_connect_light', 'Orange'),
                'lime'      => __d('bechlem_connect_light', 'Lime'),
                'cyan'      => __d('bechlem_connect_light', 'Cyan'),
                'gray'      => __d('bechlem_connect_light', 'Gray'),
                'gray-dark' => __d('bechlem_connect_light', 'Gray-Dark'),
                'dark'      => __d('bechlem_connect_light', 'Dark'),
            ]; ?>
            <?= $this->Form->control('value', [
                'type'      => 'select',
                'label'     => __d('bechlem_connect_light', 'Box color'),
                'default'   => h($backendBoxColor),
                'options'   => h($backendBoxColorOptions),
                'class'     => 'custom-select mb-3 border-0 text-' . h($backendButtonColor) . ' bg-' . h($backendBoxColor),
                'style'     => 'width: 100%',
                'empty'     => false,
                'required'  => true,
                'onchange'  => 'this.form.submit();',
            ]); ?>
        <?= $this->Form->end(); ?>

</aside>

<?= $this->Html->scriptBlock(
    '$(function() {
        $(\'.customize-the-backend\').hide();
    });',
    ['block' => 'scriptBottom']); ?>