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
namespace BechlemConnectLight\Event;

use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Event\EventManager as CakeEventManager;
use Cake\Log\Log;

/**
 * BechlemConnectLight Event Manager class
 *
 * Descendant of EventManager, customized to map event listener objects
 */
class EventManager extends CakeEventManager
{

    /**
     * A map of registered event listeners
     *
     * @var array
     */
    protected $_listenersMap = [];

    /**
     * Load Event Handlers during bootstrap.
     *
     * Plugins can add their own custom EventHandler in Config/events.php
     * with the following format:
     *
     * return array(
     *     'EventHandlers' => array(
     *         'Example.ExampleEventHandler' => array(
     *             'eventKey' => null,
     *             'options' => array(
     *                 'priority' => 1,
     *                 'passParams' => false,
     *                 'className' => 'Plugin.ClassName',
     *      )));
     *
     * @return void
     */
    public static function loadListeners()
    {
        $plugins = Configure::read('plugins');
        foreach ($plugins as $key => $plugin) {
            $file = $plugin . DS . 'config' . DS . 'events.php';
            if (file_exists($file)) {
                Configure::load($key . '.' . 'events');
            }
        }

        $eventManager = EventManager::instance();
        $eventHandlers = Configure::read('EventHandlers');
        $validKeys = ['eventKey' => null, 'options' => []];
        $cached = [];
        if (!empty($eventHandlers) && is_array($eventHandlers)) {
            foreach ($eventHandlers as $eventHandler => $eventOptions) {
                $eventKey = null;
                if (is_numeric($eventHandler)) {
                    $eventHandler = $eventOptions;
                    $eventOptions = [];
                }
                list($plugin, $class) = pluginSplit($eventHandler);
                if (!empty($eventOptions)) {
                    extract(array_intersect_key($eventOptions, $validKeys));
                }
                if (isset($eventOptions['options']['className'])) {
                    list($plugin, $class) = pluginSplit($eventOptions['options']['className']);
                }
                $class = App::className($eventHandler, 'Event');
                if (!is_null($class) && class_exists($class)) {
                    $cached[] = compact('plugin', 'class', 'eventKey', 'eventOptions');
                } else {
                    Log::error(__d('bechlem_connect_light', 'EventHandler {0} not found in plugin {1}.', $class, $plugin));
                }
            }
        }

        foreach ($cached as $cache) {
            extract($cache);
            if (Plugin::isLoaded($plugin)) {
                $class = App::className($class, 'Event');
                $settings = isset($eventOptions['options'])? $eventOptions['options']: [];
                $listener = new $class($settings);
                $eventManager->on($listener);
            }
        }
    }

    /**
     * Adds a new listener to an event.
     *
     * @param \Cake\Event\EventListenerInterface|callable $callable
     * @param string|null $eventKey
     * @param array $options
     * @return void
     */
    public function attach($callable, string $eventKey = null, array $options = [])
    {
        parent::on($callable, $eventKey, $options);
        if (is_object($callable)) {
            $key = get_class($callable);
            $this->_listenersMap[$key] = $callable;
        }
    }

    /**
     * Removes a listener from the active listeners.
     *
     * @param \Cake\Event\EventListenerInterface|callable $callable
     * @param string|null $eventKey
     * @return void
     */
    public function detach($callable, string $eventKey = null)
    {
        if (is_object($callable)) {
            $key = get_class($callable);
            unset($this->_listenersMap[$key]);
        }
        parent::off($callable, $eventKey);
    }

    /**
     * Detach all listener objects belonging to a plugin.
     *
     * @param $plugin string
     * @return void
     */
    public function detachPluginSubscribers($plugin)
    {
        $eventHandlers = Configure::read('EventHandlers');
        if (empty($eventHandlers)) {
            return;
        }
        $eventHandlers = array_keys($eventHandlers);
        $eventHandlers = preg_grep('/^' . preg_quote($plugin, '/') . '/', $eventHandlers);
        foreach ($eventHandlers as $eventHandler) {
            $className = App::className($eventHandler, 'Event');
            if (isset($this->_listenersMap[$className])) {
                $this->detach($this->_listenersMap[$className]);
            }
        }
    }
}
