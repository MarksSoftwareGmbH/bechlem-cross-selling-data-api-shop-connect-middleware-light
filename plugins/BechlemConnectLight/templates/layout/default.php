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
    <title><?= $siteTitle; ?> | <?= h($this->fetch('title')); ?></title><?= $this->Html->charset(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <?= $this->Html->meta('favicon.ico', isset($settings['siteFaviconIcoUrl'])? $settings['siteFaviconIcoUrl']: '', ['type' => 'icon']); ?>
    <?= $this->fetch('meta'); ?>
    <link rel="canonical" href="<?= $this->Url->build(); ?>" />
    <?= $this->Html->css([
        'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'fontawesome-free' . DS . 'css' . DS . 'all.min',
        'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'ionicons' . DS . '2.0.1' . DS . 'css' . DS . 'ionicons.min',
        'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'tempusdominus-bootstrap-4' . DS . 'css' . DS . 'tempusdominus-bootstrap-4.min',
        'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'icheck-bootstrap' . DS . 'icheck-bootstrap.min',
        'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'jqvmap' . DS . 'jqvmap.min',
        'BechlemConnectLight' . '.' . 'admin' . DS . 'adminlte.min',
        'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'overlayScrollbars' . DS . 'css' . DS . 'OverlayScrollbars.min',
        'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'daterangepicker' . DS . 'daterangepicker',
        'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'summernote' . DS . 'summernote-bs4',
        'BechlemConnectLight' . '.' . 'admin' . DS . 'fonts',
    ]); ?><?= $this->fetch('css'); ?><?= $this->fetch('script'); ?></head>
    <?php
        if ($activeAction === 'register'):
            echo $this->element('Users/register');
        elseif ($activeAction === 'forgot'):
            echo $this->element('Users/forgot');
        elseif ($activeAction === 'reset'):
            echo $this->element('Users/reset');
        else:
            echo $this->element('Users/login');
        endif;
    ?>
</html>
