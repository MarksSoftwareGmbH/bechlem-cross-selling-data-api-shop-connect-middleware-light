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
namespace BechlemConnectLight\View\Helper;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Class MenuHelper
 *
 * @package BechlemConnectLight\View\Helper
 */
class MenuHelper extends Helper
{
    /**
     * Helpers array
     *
     * @var array
     */
    public array $helpers = ['Html'];

    /**
     * Build a Menu (UL/OL) out of MenuHandlers array.
     *
     * @param array|null $requestParams
     * @return mixed
     */
    public function menu($requestParams = null)
    {
        $menu = $this->Html->tag('li',
            $this->Html->link(
                $this->Html->tag('i', '', ['class' => 'nav-icon fas fa-th'])
                . $this->Html->tag('p', __d('bechlem_connect_light', 'Dashboard')),
                [
                    'prefix'        => 'Admin',
                    'plugin'        => 'BechlemConnectLight',
                    'controller'    => 'Dashboards',
                    'action'        => 'dashboard',
                ],
                [
                    'class'     => ($requestParams['action'] === 'dashboard')? 'nav-link active': 'nav-link',
                    'escape'    => false,
                ]),
            ['class' => 'nav-item']);
        $menu .= $this->_menuItems(Configure::read('MenuHandlers'), $requestParams);

        return $this->Html->tag('ul', $menu, [
            'class' => 'nav nav-pills nav-sidebar flex-column text-sm nav-child-indent',
            'data-widget' => 'treeview',
            'role' => 'menu',
            'data-accordion' => 'false',
        ]);
    }

    /**
     * Internal function to build a nested menu list out of MenuHandlers array.
     *
     * @param array $menuHandlers
     * @param array|null $requestParams
     * @return string
     */
    protected function _menuItems(array $menuHandlers, array $requestParams = null)
    {
        $menuItems = '';
        if (!empty($menuHandlers) && is_array($menuHandlers)) {
            $menuHandlers = Hash::sort($menuHandlers, '{s}.position', 'asc');
            foreach ($menuHandlers as $item) {
                if (!empty($item['branch']) && is_array($item['branch'])) {
                    $menuItems .= $this->_menuItemTree($item, $requestParams);
                } else {
                    $menuItems .= $this->_menuItem($item, $requestParams);
                }
            }
        }

        return $menuItems;
    }

    /**
     * Internal function to build a menu item out of item array.
     *
     * @param array $item
     * @param array|null $requestParams
     * @return mixed
     */
    protected function _menuItem(array $item, array $requestParams = null)
    {
        $link = $this->Html->link(
            $this->Html->icon($item['icon'], ['class' => 'nav-icon'])
            . ' '
            . $this->Html->tag('p', $item['title']),
            $item['link'],
            $item['options'] + [
                'class' => ($item['controller'] === $requestParams['controller'])? 'nav-link active': 'nav-link'
            ]);

        return $this->Html->tag('li', $link, ['class' => 'nav-item']);
    }

    /**
     * Internal function to build a nested menu item list out of item array.
     *
     * @param array $item
     * @param array|null $requestParams
     * @return mixed
     */
    protected function _menuItemTree(array $item, array $requestParams = null)
    {
        $menuItemTree = $this->Html->link(
            $this->Html->icon($item['icon'], ['class' => 'nav-icon'])
            . ' '
            . $this->Html->tag(
                'p',
                $item['title']
                . ' '
                . $this->Html->icon('angle-left', ['class' => 'right'])
            ),
            'javascript:void(0)',
            $item['options'] + [
                'class' => in_array($requestParams['controller'], $item['controller'])? 'nav-link active': 'nav-link'
            ]);

        $menuItem = '';
        if (!empty($item['branch']) && is_array($item['branch'])) {
            $item['branch'] = Hash::sort($item['branch'], '{s}.position', 'asc');
            foreach ($item['branch'] as $branch) {
                $menuItem .= $this->_menuItem($branch, $requestParams);
            }
        }
        $menuItemTree .= $this->Html->tag('ul', $menuItem, ['class' => 'nav nav-treeview']);

        return $this->Html->tag(
            'li',
            $menuItemTree,
            ['class' => in_array($requestParams['controller'], $item['controller'])? 'nav-item has-treeview menu-open': 'nav-item has-treeview']
        );
    }
}
