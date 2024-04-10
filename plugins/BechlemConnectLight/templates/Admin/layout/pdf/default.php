<!DOCTYPE html>
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

$backendLinkTextColor = 'navy';
if (Configure::check('BechlemConnectLight.settings.backendLinkTextColor')):
    $backendLinkTextColor = Configure::read('BechlemConnectLight.settings.backendLinkTextColor');
endif;

$backendControlSidebar = '0';
if (Configure::check('BechlemConnectLight.settings.backendControlSidebar')):
    $backendControlSidebar = Configure::read('BechlemConnectLight.settings.backendControlSidebar');
endif;

// Get browser language first
$language = 'en';
if (!empty(getEnv('HTTP_ACCEPT_LANGUAGE'))):
    $language = substr(getEnv('HTTP_ACCEPT_LANGUAGE'), 0, 2);
endif;
// If session locale set use it as language
if ($session->check('Locale.code')):
    $language = substr($session->read('Locale.code'), 0, 2);
endif;

// Set active controller
$activeController = $this->getRequest()->getParam('controller');

// Set active action
$activeAction = $this->getRequest()->getParam('action');

// Define global settings
$settings = [];
if (!empty($settings_for_layout)):
    $settings = $settings_for_layout['settings'];
endif;
// Site title
$siteTitle = isset($settings['siteTitle'])? $settings['siteTitle']: '';
if (isset($settings['siteTitlePrefix']) & !empty($settings['siteTitlePrefix'])):
    $siteTitle = h($settings['siteTitlePrefix']) . ' | ' . $siteTitle;
endif;
if (isset($settings['siteTitleSuffix']) & !empty($settings['siteTitleSuffix'])):
    $siteTitle = $siteTitle . ' | ' . h($settings['siteTitleSuffix']);
endif;
// Define global locales
$locales = [];
if (!empty($locales_for_layout)):
    $locales = $locales_for_layout['locales'];
endif;
?>
<html lang="<?= $language; ?>">
    <head>
        <title></title>
        <?= $this->Html->charset(); ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <?= $this->Html->meta('favicon.ico', DS . 'BechlemConnectLight' . DS . 'img' . DS . 'admin' . DS . 'favicons' . DS . 'favicon.ico', ['type' => 'icon']); ?>
        <?= $this->fetch('meta'); ?>
        <link rel="canonical" href="<?= $this->Url->build(); ?>" />
        <?= $this->Html->css([
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'vendor' . DS . 'fontawesome-free' . DS . 'css' . DS . 'all.min',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'vendor' . DS . 'ionicons' . DS . '2.0.1' . DS . 'css' . DS . 'ionicons.min',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'vendor' . DS . 'tempusdominus-bootstrap-4' . DS . 'css' . DS . 'tempusdominus-bootstrap-4.min',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'vendor' . DS . 'icheck-bootstrap' . DS . 'icheck-bootstrap.min',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'vendor' . DS . 'jqvmap' . DS . 'jqvmap.min',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'adminlte.min',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'vendor' . DS . 'overlayScrollbars' . DS . 'css' . DS . 'OverlayScrollbars.min',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'vendor' . DS . 'daterangepicker' . DS . 'daterangepicker',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'vendor' . DS . 'summernote' . DS . 'summernote-bs4',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'fonts',
            ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'css' . DS . 'admin' . DS . 'bechlem_connect_light.admin'
        ]); ?>
        <?= $this->fetch('css'); ?>
        <?= $this->fetch('script'); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'jquery' . DS . 'jquery.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'jquery-ui' . DS . 'jquery-ui.min', ['block' => 'scripts']); ?>
        <?= $this->Html->scriptBlock("$.widget.bridge('uibutton', $.ui.button)", ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'bootstrap' . DS . 'js' . DS . 'bootstrap.bundle.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'jquery-validation' . DS . 'jquery.validate.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'jquery-validation' . DS . 'additional-methods.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'jqvmap' . DS . 'jquery.vmap.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'jqvmap' . DS . 'maps' . DS . 'jquery.vmap.world', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'jquery-knob' . DS . 'jquery.knob.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'moment' . DS . 'moment.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'daterangepicker' . DS . 'daterangepicker', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'tempusdominus-bootstrap-4' . DS . 'js' . DS . 'tempusdominus-bootstrap-4.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'summernote' . DS . 'summernote-bs4.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'vendor' . DS . 'overlayScrollbars' . DS . 'js' . DS . 'jquery.overlayScrollbars.min', ['block' => 'scripts']); ?>
        <?= $this->Html->script(ROOT . DS .  'plugins' . DS . 'BechlemConnectLight' . DS . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'adminlte.min', ['block' => 'scripts']); ?>
        <?= $this->fetch('scripts'); ?>
        <?= $this->fetch('scriptBottom'); ?>
    </head>
    <body class="hold-transition sidebar-mini layout-fixed accent-<?= h($backendLinkTextColor); ?> control-sidebar-slide-open text-sm">
        <div class="wrapper">
            <div class="content-wrapper">
                <section class="content">
                    <div class="container-fluid">
                        <?= $this->fetch('content'); ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <small><?= __d('bechlem_connect_light',
                                    '&copy; Copyright {date} {link}',
                                    [
                                        'date' => $this->Time->format($this->Time->gmt(), 'Y'),
                                        'link' => $this->Html->link(
                                            'Marks Software GmbH',
                                            'https://www.marks-software.de/',
                                            [
                                                'style'         => 'color: #941342;',
                                                'target'        => '_blank',
                                                'escapeTitle'   => false,
                                            ]
                                        ),
                                    ]); ?>
                                <?= __d('bechlem_connect_light', 'All rights reserved.'); ?></small>
                                <small><?= __d('bechlem_connect_light',
                                    'Powered by {bechlemConnectLight}',
                                    ['bechlemConnectLight' => $this->Html->link(
                                        'BECHLEM CONNECT LIGHT',
                                        'https://github.com/MarksSoftwareGmbH/BECHLEM-CONNECT-LIGHT',
                                        [
                                            'style'         => 'color: #941342;',
                                            'target'        => '_blank',
                                            'escapeTitle'   => false,
                                        ]
                                    )]); ?> v<?= Configure::version(); ?></small>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>