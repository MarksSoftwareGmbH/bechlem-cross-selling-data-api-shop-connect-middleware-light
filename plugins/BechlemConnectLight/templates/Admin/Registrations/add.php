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
            'controller'    => 'Registrations',
            'action'        => 'index',
        ]
    ],
    ['title' => __d('bechlem_connect_light', 'Add registration')],
]); ?>

<?= $this->Form->create($registration, ['class' => 'form-general']); ?>
<div class="row">
    <section class="col-lg-8 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $this->Html->icon('plus'); ?> <?= __d('bechlem_connect_light', 'Add registration'); ?>
                </h3>
            </div>
            <div class="card-body">
                <?= $this->Form->control('billing_name', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_name_addition', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('billing_legal_form', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_vat_number', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_salutation', [
                    'type'      => 'select',
                    'options'   => !empty($this->BechlemConnectLight->salutations())? $this->BechlemConnectLight->salutations(): [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => false,
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_first_name', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_middle_name', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('billing_last_name', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_management', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_email', [
                    'type'      => 'email',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_website', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('billing_telephone', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_mobilephone', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('billing_fax', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('billing_street', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_street_addition', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('billing_postcode', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_city', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('billing_country', [
                    'type'      => 'select',
                    'options'   => !empty($this->BechlemConnectLight->countries())? $this->BechlemConnectLight->countries(): [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => true,
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('shipping_name', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_name_addition', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_management', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_email', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_telephone', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_mobilephone', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_fax', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_street', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_street_addition', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_postcode', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_city', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('shipping_country', [
                    'type'      => 'select',
                    'options'   => !empty($this->BechlemConnectLight->countries())? $this->BechlemConnectLight->countries(): [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => true,
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
                <?= $this->Form->control('registration_type_id', [
                    'type'      => 'select',
                    'label'     => __d('bechlem_connect_light', 'Type'),
                    'options'   => !empty($registrationTypes)? $registrationTypes: [],
                    'class'     => 'select2',
                    'style'     => 'width: 100%',
                    'empty'     => false,
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('newsletter_email', [
                    'type'      => 'email',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('register_excerpt', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <?= $this->Form->control('remark', [
                    'type'      => 'text',
                    'required'  => false,
                ]); ?>
                <?= $this->Form->control('ip', [
                    'type'      => 'text',
                    'required'  => true,
                ]); ?>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?= $this->Form->checkbox('newsletter', ['id' => 'newsletter', 'class' => 'custom-control-input', 'checked' => false, 'required' => false]); ?>
                        <label class="custom-control-label" for="newsletter"><?= __d('bechlem_connect_light', 'Newsletter'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?= $this->Form->checkbox('marketing', ['id' => 'marketing', 'class' => 'custom-control-input', 'checked' => false, 'required' => false]); ?>
                        <label class="custom-control-label" for="marketing"><?= __d('bechlem_connect_light', 'Marketing'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?= $this->Form->checkbox('terms_conditions', ['id' => 'termsConditions', 'class' => 'custom-control-input', 'checked' => false, 'required' => false]); ?>
                        <label class="custom-control-label" for="termsConditions"><?= __d('bechlem_connect_light', 'Terms conditions'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <?= $this->Form->checkbox('privacy_policy', ['id' => 'privacyPolicy', 'class' => 'custom-control-input', 'checked' => false, 'required' => false]); ?>
                        <label class="custom-control-label" for="privacyPolicy"><?= __d('bechlem_connect_light', 'Privacy policy'); ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <?= $this->Form->button(__d('bechlem_connect_light', 'Submit'), ['class' => 'btn btn-success']); ?>
                    <?= $this->Html->link(
                        __d('bechlem_connect_light', 'Cancel'),
                        [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'Registrations',
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

<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'select2' . DS . 'css' . DS . 'select2.min'); ?>
<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'bechlem_connect_light.select2'); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'select2' . DS . 'js' . DS . 'select2.full.min',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'template' . DS . 'admin' . DS . 'registrations' . DS . 'form',
    ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock(
    '$(function() {
        Registrations.init();
        // Initialize select2
        $(\'.select2\').select2();
        $(\'.form-general\').validate({
            rules: {
                billing_name: {
                    required: true
                },
                billing_vat_number: {
                    required: true
                }
            },
            messages: {
                billing_name: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid billing name') . '\'
                },
                billing_vat_number: {
                    required: \'' . __d('bechlem_connect_light', 'Please enter a valid billing vat number') . '\'
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
