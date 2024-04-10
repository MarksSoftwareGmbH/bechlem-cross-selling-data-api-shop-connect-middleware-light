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
?>
<div class="row">
    <div class="col-lg-12">
        <h1><?= __d('bechlem_connect_light', 'MIT license documentation'); ?></h1>
        <p><strong><?= __d(
            'bechlem_connect_light',
            'BECHLEM CONNECT "LIGHT" is an open source project that is licensed under the {mitLicense}. This allows you to do pretty much anything you want as long as you include the copyright in “all copies or substantial portions of the Software”.',
            ['mitLicense' => $this->Html->link(
                __d('bechlem_connect_light', 'MIT license'),
                'https://opensource.org/license/MIT',
                [
                    'style'     => 'color: #941342;',
                    'target'    => '_blank',
                    'title'     => __d('bechlem_connect_light', 'MIT license'),
                    'escape'    => false,
                ])]); ?></strong></p>
        <h5 class="text-bold text-dark mt-3">
            <?= __d('bechlem_connect_light', 'What you are {allowed} to do with BECHLEM CONNECT "LIGHT"', ['allowed' => $this->Html->tag('span', __d('bechlem_connect_light', 'ALLOWED'), ['style' => 'color: #28a745;'])]); ?>:
        </h5>
        <ol>
            <li><?= __d('bechlem_connect_light', 'Use in commercial projects.'); ?></li>
            <li><?= __d('bechlem_connect_light', 'Use in personal / private projects.'); ?></li>
            <li><?= __d('bechlem_connect_light', 'Modify and change the work.'); ?></li>
            <li><?= __d('bechlem_connect_light', 'Distribute the code.'); ?></li>
            <li><?= __d('bechlem_connect_light', 'Sublicense: incorporate the work into something that has a more restrictive license.'); ?></li>
        </ol>
        <h5 class="text-bold text-dark mt-3">
            <?= __d('bechlem_connect_light', 'What you {must} to do with BECHLEM CONNECT "LIGHT"', ['must' => $this->Html->tag('span', __d('bechlem_connect_light', 'MUST'), ['style' => 'color: #ffc107;'])]); ?>:
        </h5>
        <ol>
            <li><?= __d('bechlem_connect_light', 'Include the license notice in all copies of the work.'); ?></li>
            <li><?= __d('bechlem_connect_light', 'Include the copyright notice in all copies of the work. This applies to everything inclusive the copyright notice in the application footer.'); ?></li>
        </ol>
        <h5 class="text-bold text-dark mt-3">
            <?= __d('bechlem_connect_light', 'What you are {notAllowed} to do with BECHLEM CONNECT "LIGHT"', ['notAllowed' => $this->Html->tag('span', __d('bechlem_connect_light', 'NOT ALLOWED'), ['style' => 'color: #dc3545;'])]); ?>:
        </h5>
        <ol>
            <li><?= __d('bechlem_connect_light', 'The work is provided “as is”. You may not hold the author liable.'); ?></li>
        </ol>
    </div>
</div>