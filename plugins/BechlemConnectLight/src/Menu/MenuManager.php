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
namespace BechlemConnectLight\Menu;

use Cake\Core\Configure;

/**
 * Menu manager
 *
 * Class MenuManager
 * @package BechlemConnectLight\Menu
 */
class MenuManager
{

    /**
     * The globally available instance, used for dispatching menus attached from any scope
     *
     * @var \BechlemConnectLight\Menu\MenuManager
     */
    protected static $_generalManager = null;

    /**
     * Returns the globally available instance of a MenuManager
     * @return MenuManager the global menu manager
     */
    public static function instance($manager = null)
    {
        if ($manager instanceof MenuManager) {
            static::$_generalManager = $manager;
        }
        if (empty(static::$_generalManager)) {
            static::$_generalManager = new MenuManager();
        }

        return static::$_generalManager;
    }

    /**
     * Load Menu Handlers during bootstrap.
     *
     * Plugins can add their own custom MenuHandler in Config/menus.php
     * with the following format:
     *
     * return [
     *     'MenuHandlers' => [
     *         'Plugin.Articles' => [
     *             'controller' => 'Articles',
     *             'title' => 'Articles',
     *             'icon' => 'files-o',
     *             'link' => [
     *                 'prefix' => 'admin',
     *                 'plugin' => 'Plugin',
     *                 'controller' => 'Articles',
     *                 'action' => 'index',
     *             ],
     *            'options' => ['escape' => false],
     *            'position' => 1,
     *         ],
     *         'Plugin.ArticlesTypes' => [
     *             'controller' => [
     *                  'Articles',
     *                  'Articletypes',
     *              ],
     *             'title' => 'Articles & Types',
     *             'icon' => 'files-o',
     *             'branch' => [
     *                 'Plugin.Articles' => [
     *                     'controller' => 'Articles',
     *                     'title' => 'Articles',
     *                     'icon' => 'files-o',
     *                     'link' => [
     *                         'prefix' => 'admin',
     *                         'plugin' => 'Plugin',
     *                         'controller' => 'Articles',
     *                         'action' => 'index',
     *                     ],
     *                     'options' => ['escape' => false],
     *                     'position' => 1,
     *                 ],
     *             ],
     *            'options' => ['escape' => false],
     *            'position' => 2,
     *         ],
     *     ],
     * ];
     *
     * @return void
     */
    public static function loadListeners()
    {
        $plugins = Configure::read('plugins');
        foreach ($plugins as $key => $plugin) {
            $file = $plugin . DS . 'config' . DS . 'menus.php';
            if (file_exists($file)) {
                Configure::load($key . '.' . 'menus');
            }
        }
    }
}
