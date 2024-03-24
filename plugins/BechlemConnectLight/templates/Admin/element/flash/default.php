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

$class = isset($params['class'])? $params['class']: null;
$escape = isset($params['escape'])? $params['escape']: true;

if ($escape):
    $message = isset($message)? h($message): '';
endif;

switch ($class):
    case 'success':
        $class = 'alert alert-success alert-dismissible';
        $type = 'success';
        $icon = 'fa fa-check';
        break;
    case 'warning':
        $class = 'alert alert-warning alert-dismissible';
        $type = 'warning';
        $icon = 'fa fa-exclamation-triangle';
        break;
    case 'danger':
        $class = 'alert alert-danger alert-dismissible';
        $type = 'danger';
        $icon = 'fa fa-exclamation-triangle';
        break;
    case 'error':
        $class = 'alert alert-danger alert-dismissible';
        $type = 'danger';
        $icon = 'fa fa-exclamation-triangle';
        break;
    default:
        $class = 'alert alert-info alert-dismissible';
        $type = 'info';
        $icon = 'fa fa-info';
endswitch;

echo $this->Html->css(['BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'animate' . DS . 'animate'])
. $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'bootstrap-notify' . DS . 'bootstrap-notify.min',
    ['block' => 'scriptBottom'])
. $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'template' . DS . 'element' . DS . 'flash' . DS . 'default',
    ['block' => 'scriptBottom'])
. $this->Html->scriptBlock(
    '$(function() {
        FlashMessages.init(\'' . $icon . '\' ,\'' . $message . '\',\'' . $type . '\');
    });', ['block' => 'scriptBottom']);