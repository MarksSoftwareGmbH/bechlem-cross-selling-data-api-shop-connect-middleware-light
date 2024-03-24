<?php
declare(strict_types=1);

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
namespace BechlemConnectLight\Crud\Action;

use Crud\Action\BaseAction;
use Crud\Traits\FindMethodTrait;
use Crud\Traits\ViewTrait;
use Crud\Traits\ViewVarTrait;

/**
 * Class NumberAllAction
 *
 * @package BechlemConnectLight\Crud\Action
 */
class NumberAllAction extends BaseAction
{

    use FindMethodTrait;
    use ViewTrait;
    use ViewVarTrait;

    /**
     * Default settings for 'numberAll' action
     *
     * @var array
     */
    protected array $_defaultConfig = [
        'enabled' => true,
        'scope' => 'table',
        'findMethod' => 'all',
        'view' => null,
        'viewVar' => null,
        'serialize' => [],
        'api' => [
            'success' => [
                'code' => 200
            ],
            'error' => [
                'code' => 400
            ],
        ],
    ];

    /**
     * Generic HTTP handler
     *
     * @param string|null $number
     * @throws \Exception
     * @return void
     */
    protected function _handle(string $number = null)
    {
        $repository = $this->_table();

        $query = $repository->find($this->findMethod());
        $query->where([
            $repository->getAlias() . '.' . 'number' => $number,
        ]);

        $subject = $this->_subject(['success' => true, 'query' => $query]);

        $this->_trigger('beforePaginate', $subject);
        $subject->set(['entities' => $query]);

        $this->_trigger('afterPaginate', $subject);
        $this->_trigger('beforeRender', $subject);
    }
}
