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
var Sortable = function($url) {
    return {
        init: function($url) {
            $('#sortable tbody').sortable({
                helper: fixWidthHelper,
                cursor: 'move',
                axis: 'y',
                update: function (event, ui) {

                    var siblingId = null;
                    var siblingLft = null;
                    
                    if (ui.item.context.nextSibling.id) {
                        siblingId = ui.item.context.nextSibling.id;
                        siblingLft = ui.item.context.nextSibling.dataset.lft;
                    }
                    
                    if (ui.item.context.previousSibling.id) {
                        siblingId = ui.item.context.previousSibling.id;
                        siblingLft = ui.item.context.previousSibling.dataset.lft;
                    }
                    
                    var request = '.json?draggedId=' + ui.item.context.id
                        + '&draggedLft=' + ui.item.context.dataset.lft
                        + '&siblingId=' + siblingId
                        + '&siblingLft=' + siblingLft;
                    
                    $.getJSON($url + request, function(result) {
                        if (result.response === false) {
                            document.location.reload();
                        }
                    });
                }
            }).disableSelection().css({ cursor: 'pointer' });

            function fixWidthHelper(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            }
        }
    };
}();
