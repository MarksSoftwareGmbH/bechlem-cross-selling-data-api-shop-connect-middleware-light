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
<div
    class="modal fade"
    id="pleaseWait"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    data-backdrop="static"
    data-keyboard="false"
>
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center">
                    <span class="fa fa-spinner fa-spin"></span>
                    <?= __d('bechlem_connect_light', 'Please wait...'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-info progress-bar-striped active" style="width: 100%">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->scriptBlock(
    '$(function(){
        $(\'form\').on(\'submit\', function () {
            showMessage();
        });
        function showMessage() {
            $(\'#pleaseWait\').modal(\'show\');
            window.addEventListener(\'focus\', hideMessage, false);
        }
        function hideMessage() {
            window.removeEventListener(\'focus\', hideMessage, false);
            $(\'#pleaseWait\').modal(\'hide\');
        }
    });',
    ['block' => 'scriptBottom']); ?>
