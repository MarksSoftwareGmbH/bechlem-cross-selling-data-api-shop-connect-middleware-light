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

use ArrayObject;
use Cake\Controller\Component\AuthComponent;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

/**
 * Class LoggedInUserListener
 */
class LoggedInUserListener implements EventListenerInterface
{

    /**
     *
     * @var string
     */
    protected $_Auth;

    /**
     * Constructor hook method.
     *
     * @param AuthComponent $Auth
     */
    public function __construct(AuthComponent $Auth)
    {
        $this->_Auth = $Auth;
    }

    /**
     * Gets the Model callbacks this behavior is interested in.
     *
     * By defining one of the callback methods a behavior is assumed
     * to be interested in the related event.
     *
     * Override this method if you need to add non-conventional event listeners.
     * Or if you want your behavior to listen to non-standard events.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Model.beforeSave' => [
                'callable' => 'beforeSave',
                'priority' => -100
            ],
            'Model.beforeDelete' => [
                'callable' => 'beforeDelete',
                'priority' => -100
            ]
        ];
    }

    /**
     * Before save listener method.
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\ORM\Entity $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if (empty($options['loggedInUser'])) {
            $options['loggedInUser'] = $this->_Auth->user('id');
        }
    }

    /**
     * Before delete listener method.
     *
     * @param \Cake\Event\Event $event The beforeDelete event that was fired
     * @param \Cake\ORM\Entity $entity The entity that is going to be deleted
     * @param \ArrayObject $options the options passed to the delete method
     * @return void
     */
    public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if (empty($options['loggedInUser'])) {
            $options['loggedInUser'] = $this->_Auth->user('id');
        }
    }
}
