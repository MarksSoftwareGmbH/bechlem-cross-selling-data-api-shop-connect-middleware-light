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
use Migrations\AbstractMigration;

/**
 * Class InitialBechlemPrinters
 */
class InitialBechlemPrinters extends AbstractMigration
{
    /**
     * You can specify a autoId property in the Migration class and set it to false,
     * which will turn off the automatic id column creation.
     *
     * @var bool
     * public $autoId = false;
     */

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    // @codingStandardsIgnoreStart
    public function change()
    {
        // Table bechlem_printers
        $tableName = 'bechlem_printers';

        // Check if table exists
        $exists = $this->hasTable($tableName);
        if ($exists) {
            // Drop table
            $this->table($tableName)->drop()->save();
        }

        // Create table
        $table = $this->table($tableName, ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->addColumn('uuid_id', 'uuid', ['null' => true])
            ->addColumn('id_item', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('id_brand', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('brand', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('art_nr', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('id_category', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('category', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('printer_series', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('ean', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('picture', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('language', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('created_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addColumn('modified_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('deleted', 'datetime', ['null' => true])
            ->addColumn('deleted_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addIndex(['id_item'])
            ->addIndex(['id_brand'])
            ->addIndex(['name'])
            ->create();
    }
    // @codingStandardsIgnoreEnd
}
