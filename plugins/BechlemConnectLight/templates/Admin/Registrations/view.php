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
    . h($registration->id) . ' ' . '(' . h($registration->billing_name) . ')'
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
    ['title' => __d('bechlem_connect_light', 'View')],
    ['title' => h($registration->id) . ' ' . '(' . h($registration->billing_name) . ')']
]); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= h($registration->id) . ' ' . '(' . h($registration->billing_name) . ')'; ?> - <?= __d('bechlem_connect_light', 'View'); ?>
                </h3>
                <div class="card-tools">
                    <?= $this->Form->create(null, [
                        'url' => [
                            'plugin'        => 'BechlemConnectLight',
                            'controller'    => 'Registrations',
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
                                    'controller'    => 'Registrations',
                                    'action'        => 'index',
                                ],
                                [
                                    'class'         => 'btn btn-' . h($backendButtonColor),
                                    'escapeTitle'   => false,
                                ]
                            ),
                    ]); ?>
                    <?= $this->Form->end(); ?>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Id'); ?></dt>
                    <dd class="col-sm-9"><?= h($registration->id); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Type'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($registration->registration_type->title)): ?>
                            <?= $registration->has('registration_type')?
                                $this->Html->link(
                                    h($registration->registration_type->title),
                                    [
                                        'plugin'        => 'BechlemConnectLight',
                                        'controller'    => 'RegistrationTypes',
                                        'action'        => 'view',
                                        'id'            => h($registration->registration_type->id),
                                    ]): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_name)? '-': h($registration->billing_name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing name addition'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_name_addition)? '-': h($registration->billing_name_addition); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing legal form'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_legal_form)? '-': h($registration->billing_legal_form); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing vat number'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_vat_number)? '-': h($registration->billing_vat_number); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing salutation'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_salutation)? '-': h($registration->billing_salutation); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing first name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_first_name)? '-': h($registration->billing_first_name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing middle name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_middle_name)? '-': h($registration->billing_middle_name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing last name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_last_name)? '-': h($registration->billing_last_name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing management'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_management)? '-': h($registration->billing_management); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing email'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($registration->billing_email)): ?>
                            <?= $registration->has('billing_email')? $this->Html->link(h($registration->billing_email), 'mailto:' . h($registration->billing_email)): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing website'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($registration->billing_website)): ?>
                            <?= $registration->has('billing_website')? $this->Html->link(h($registration->billing_website), h($registration->billing_website), ['target' => '_blank']): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing telephone'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_telephone)? '-': h($registration->billing_telephone); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing mobilephone'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_mobilephone)? '-': h($registration->billing_mobilephone); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing fax'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_fax)? '-': h($registration->billing_fax); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing street'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_street)? '-': h($registration->billing_street); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing street addition'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_street_addition)? '-': h($registration->billing_street_addition); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing postcode'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_postcode)? '-': h($registration->billing_postcode); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing city'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_city)? '-': h($registration->billing_city); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Billing country'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->billing_country)? '-': h($registration->billing_country); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping name'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_name)? '-': h($registration->shipping_name); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping name addition'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_name_addition)? '-': h($registration->shipping_name_addition); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping management'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_management)? '-': h($registration->shipping_management); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping email'); ?></dt>
                    <dd class="col-sm-9">
                        <?php if (!empty($registration->shipping_email)): ?>
                            <?= $registration->has('shipping_email')? $this->Html->link(h($registration->shipping_email), 'mailto:' . h($registration->shipping_email)): '-'; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping telephone'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_telephone)? '-': h($registration->shipping_telephone); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping mobilephone'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_mobilephone)? '-': h($registration->shipping_mobilephone); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping fax'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_fax)? '-': h($registration->shipping_fax); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping street'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_street)? '-': h($registration->shipping_street); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping street addition'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_street_addition)? '-': h($registration->shipping_street_addition); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping postcode'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_postcode)? '-': h($registration->shipping_postcode); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping city'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_city)? '-': h($registration->shipping_city); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Shipping country'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->shipping_country)? '-': h($registration->shipping_country); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Newsletter email'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->newsletter_email)? '-': h($registration->newsletter_email); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Remark'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->remark)? '-': h($registration->remark); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Register excerpt'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->register_excerpt)? '-': h($registration->register_excerpt); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Newsletter'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($registration->newsletter)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Marketing'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($registration->marketing)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Terms conditions'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($registration->terms_conditions)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Privacy policy'); ?></dt>
                    <dd class="col-sm-9"><?= $this->BechlemConnectLight->status(h($registration->privacy_policy)); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'IP'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->ip)? '-': h($registration->ip); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Created'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->created)? '-': h($registration->created->format('d.m.Y H:i:s')); ?></dd>
                    <dt class="col-sm-3"><?= __d('bechlem_connect_light', 'Modified'); ?></dt>
                    <dd class="col-sm-9"><?= empty($registration->modified)? '-': h($registration->modified->format('d.m.Y H:i:s')); ?></dd>
                </dl>
                <hr/>
                <dl>
                    <dd>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="heading">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                                            <?= __d('bechlem_connect_light', 'REST API request') . ':' . ' ' . '/api/registrations/' . h($registration->id) . ' ' . '(' . __d('bechlem_connect_light', 'JSON decoded version') . ')'; ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <pre>
                                            <code class="language-php">
                                                <?php $json = json_encode(['success' => true, 'data' => $registration]); ?>
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
                        'controller'    => 'Registrations',
                        'action'        => 'index',
                    ],
                    [
                        'class'         => 'btn btn-app',
                        'escapeTitle'   => false,
                    ]); ?>
                <?= $this->Html->link(
                    $this->Html->icon('edit') . ' ' . __d('bechlem_connect_light', 'Edit'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Registrations',
                        'action'        => 'edit',
                        'id'            => h($registration->id),
                    ],
                    [
                        'class'         => 'btn btn-app',
                        'escapeTitle'   => false,
                    ]); ?>
                <?= $this->Form->postLink(
                    $this->Html->icon('trash') . ' ' . __d('bechlem_connect_light', 'Delete'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Registrations',
                        'action'        => 'delete',
                        'id'            => h($registration->id),
                    ],
                    [
                        'confirm' => __d(
                            'bechlem_connect_light',
                            'Are you sure you want to delete "{billingName}"?',
                            ['billingName' => h($registration->billing_name)]
                        ),
                        'class'         => 'btn btn-app',
                        'escapeTitle'   => false,
                    ]); ?>
            </div>
        </div>
    </div>
</div>

