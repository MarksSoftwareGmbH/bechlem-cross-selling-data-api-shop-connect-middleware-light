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
namespace BechlemConnectLight\Utility;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Utility\Hash;
use Cake\Http\ServerRequest;
use Cake\Utility\Inflector;

class BechlemConnectLight
{

    /**
     * Loads as a normal component from controller.
     *
     * @param string $controllerName Controller Name
     * @param mixed $componentName Component name or array of Component and settings
     */
    public static function hookComponent($controllerName, $componentName)
    {
        if (is_string($componentName)) {
            $componentName = [$componentName];
        }
        self::hookControllerProperty($controllerName, '_appComponents', $componentName);
    }

    /**
     * Hook controller property
     *
     * @param string $controllerName Controller name (for e.g., BechlemConnectLight.Global)
     * @param string $property for e.g., components
     * @param string|array $value
     */
    public static function hookControllerProperty($controllerName, $property, $value)
    {
        $configKeyPrefix = 'Hook.controller_properties';
        $controllerClass = self::_getClassName($controllerName, 'Controller', 'Controller');
        if ($controllerClass) {
            self::_hookProperty($configKeyPrefix, $controllerClass, $property, $value);
        }
    }

    /**
     * Hook property
     *
     * @param string $configKeyPrefix
     * @param string $name
     * @param string $property
     * @param string $value
     */
    protected static function _hookProperty($configKeyPrefix, $name, $property, $value)
    {
        $propertyValue = Configure::read($configKeyPrefix . '.' . $name . '.' . $property);
        if (!is_array($propertyValue)) {
            $propertyValue = null;
        }
        if (is_array($value)) {
            if (is_array($propertyValue)) {
                $propertyValue = Hash::merge($propertyValue, $value);
            } else {
                $propertyValue = $value;
            }
        } else {
            $propertyValue = $value;
        }

        Configure::write($configKeyPrefix . '.' . $name . '.' . $property, $propertyValue);
    }

    /**
     * Get class name property
     *
     * @param $name
     * @param $type
     * @param $suffix
     * @return bool|string
     */
    protected static function _getClassName($name, $type, $suffix)
    {
        if ($name !== '*') {
            return App::className($name, $type, $suffix);
        }

        return '*';
    }

    /**
     * Options property
     *
     * @param string $configKey
     * @param object $object
     * @param string|null $option
     * @return array|mixed|void
     */
    public static function options($configKey, object $object, string $option = null)
    {
        if (is_string($object)) {
            $objectName = $object;
        } elseif ($object instanceof ServerRequest) {
            $pluginPath = $controller = null;
            $namespace = 'Controller';
            if (!empty($object->getParam('plugin'))) {
                $pluginPath = $object->getParam('plugin') . '.';
            }
            if (!empty($object->getParam('controller'))) {
                $controller = $object->getParam('controller');
            }
            if (!empty($object->getParam('prefix'))) {
                $prefixes = array_map(
                    'Cake\Utility\Inflector::camelize',
                    explode('/', $object->getParam('prefix'))
                );
                $namespace .= '/' . implode('/', $prefixes);
            }
            $objectName = App::className($pluginPath . $controller, $namespace, 'Controller');
        } elseif (is_object($object)) {
            $objectName = get_class($object);
        } else {
            return;
        }
        $options = Configure::read($configKey . '.' . $objectName);
        if (is_array(Configure::read($configKey . '.*'))) {
            $options = Hash::merge(Configure::read($configKey . '.*'), $options);
        }
        if ($option) {
            return $options[$option];
        }

        return $options;
    }

    /**
     * Convenience method to dispatch event.
     *
     * Creates, dispatches, and returns a new Event object.
     *
     * @see Event::__construct()
     * @param string $name Name of the event
     * @param object|null $subject the object that this event applies to
     * @param array|null $data any value you wish to be transported with this event
     *
     * @return \Cake\Event\EventInterface
     */
    public static function dispatchEvent(string $name, object $subject = null, array $data = null)
    {
        $event = new Event($name, $subject, $data);
        if ($subject) {
            $event = $subject->getEventManager()->dispatch($event);
        } else {
            $event = EventManager::instance()->dispatch($event);
        }

        return $event;
    }
}
