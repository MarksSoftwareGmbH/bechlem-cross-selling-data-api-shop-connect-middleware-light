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
use Cake\Error\Debugger;

$this->layout = 'BechlemConnectLight/error';

if (Configure::read('debug')):
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.php');

    $this->start('file');
?>
<?php if (!empty($error->queryString)): ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString); ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)): ?>
    <strong>SQL Query Params: </strong>
    <?php Debugger::dump($error->params); ?>
<?php endif; ?>
<?php if ($error instanceof Error): ?>
    <strong>Error in: </strong>
    <?= sprintf('%s, line %s', str_replace(ROOT, 'ROOT', $error->getFile()), $error->getLine()); ?>
<?php endif; ?>
<?php
    echo $this->element('auto_table_warning');
    $this->end();
endif;
?>
<h2><?= __d('bechlem_connect_light', 'An internal error has occurred'); ?></h2>
<p class="error">
    <strong><?= __d('bechlem_connect_light', 'Error') ?>:</strong>
    <?= h($message); ?>
</p>
