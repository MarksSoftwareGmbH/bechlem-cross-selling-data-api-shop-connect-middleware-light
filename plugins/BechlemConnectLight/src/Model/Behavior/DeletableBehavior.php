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
namespace BechlemConnectLight\Model\Behavior;

use Cake\I18n\DateTime;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\ORM\Query\SelectQuery;
use Cake\Event\Event;

/**
 * Deletable behavior
 */
class DeletableBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected array $_defaultConfig = [
        'column' => 'deleted',
    ];

    /**
     * Default table.
     *
     * @var Table
     */
    protected $table;

    /**
     * Constructor
     *
     * Merges config with the default and store in the config property
     *
     * @param \Cake\ORM\Table $table The table this behavior is attached to.
     * @param array $config The config for this behavior.
     */
    public function __construct(Table $table, array $config = [])
    {
        $this->table = $table;
        $this->setConfig($config);
    }

    /**
     * Before detele listener method.
     *
     * @param Event $event
     * @param Entity $entity
     * @param \ArrayObject $options
     * @return bool
     */
    public function beforeDelete(Event $event, Entity $entity, \ArrayObject $options)
    {
        $event->stopPropagation();
        $dateTime = DateTime::now();

        $entity->set($this->getConfig('column'), $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'));

        if ($this->table->save($entity, ['validate' => false])) {
            $this->table->associations()->cascadeDelete(
                $entity,
                ['_primary' => false] + $options->getArrayCopy()
            );

            return true;
        }

        return false;
    }

    /**
     * Before find listener method.
     *
     * @param Event $event
     * @param SelectQuery $query
     * @param \ArrayObject $options
     */
    public function beforeFind(Event $event, SelectQuery $query, \ArrayObject $options)
    {
        if (isset($options['withDeleted']) === false || $options['withDeleted'] !== true) {
            $query->where([sprintf('%s IS', $this->column()) => null]);
        }
    }

    /**
     * Restore method.
     *
     * @param $id
     * @return bool|\Cake\Datasource\EntityInterface|mixed
     */
    public function restore($id)
    {
        $entity = $this->table->get($id, ['withDeleted' => true]);
        $entity->set($this->getConfig('column'), null);

        return $this->table->save($entity);
    }

    /**
     * Column method.
     *
     * @return string
     */
    private function column()
    {
        return sprintf('%s.%s', $this->table->getAlias(), $this->getConfig('column'));
    }
}
