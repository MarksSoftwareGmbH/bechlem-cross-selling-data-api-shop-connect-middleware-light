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

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use BechlemConnectLight\Utility\BechlemConnectLight;
use BechlemConnectLight\Controller\HookableComponentInterface;

class HookableComponentEventHandler implements EventListenerInterface
{

    /**
     * Implemented events method.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Controller.afterInitialize' => 'initialize',
        ];
    }

    /**
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function initialize(Event $event)
    {
        /* @var \Cake\Controller\Controller|\BechlemConnectLight\Controller\HookableComponentInterface $controller */
        $controller = $event->getSubject();

        $components = $this->_getComponents($controller);
        foreach ($components as $component => $config) {
            $controller->_loadHookableComponent($component, $config);
        }
    }

    /**
     * Setup the components array
     *
     * @param void
     * @return void
     */
    protected function _setupComponents()
    {
        $components = [];

        $components = Hash::merge(
            $this->_defaultComponents,
            $this->_appComponents
        );

        foreach ($components as $component => $config) {
            if (!is_array($config)) {
                $component = $config;
                $config = [];
            }

            $this->loadComponent($component, $config);
        }
    }

    /**
     * Load component
     *
     * @param $name
     * @param array $config
     * @return mixed
     */
    public function loadComponent($name, array $config = [])
    {
        [, $prop] = pluginSplit($name);
        [, $modelProp] = pluginSplit($this->defaultTable);
        $component = $this->components()->load($name, $config);
        if ($prop !== $modelProp) {
            $this->{$prop} = $component;
        }

        return $component;
    }

    /**
     * Get Components
     *
     * @param Controller $controller
     * @return array
     */
    private function _getComponents(Controller $controller)
    {
        $properties = BechlemConnectLight::options('Hook.controller_properties', $controller->getRequest());

        $components = [];
        foreach ($properties['_appComponents'] as $component => $config) {
            if (!is_array($config)) {
                $component = $config;
                $config = [];
            }

            $config = Hash::merge(['priority' => 10], $config);

            $components[$component] = $config;
        }

        uasort($components, function ($previous, $next) {
            $previousPriority = $previous['priority'];
            $nextPriority = $next['priority'];

            if ($previousPriority === $nextPriority) {
                return 0;
            }

            return ($previousPriority < $nextPriority) ? -1 : 1;
        });

        return $components;
    }
}
