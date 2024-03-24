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
 * Class InitialProducts
 */
class InitialProducts extends AbstractMigration
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
    public function change()
    {
        // Table products
        $tableName = 'products';

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
            ->addColumn('product_type_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('product_condition_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('product_delivery_time_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('product_manufacturer_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('product_tax_class_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('uuid_id', 'uuid', ['null' => true])
            ->addColumn('foreign_key', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('employee_key', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('manufacturer_key', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('manufacturer_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('manufacturer_sku', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('category_key', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('category_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('sku', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('ean', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('name', 'text', ['null' => true])
            ->addColumn('slug', 'text', ['null' => true])
            ->addColumn('stock', 'decimal', ['precision' => 15, 'scale' => 4, 'default' => 0.0000])
            ->addColumn('price', 'decimal', ['precision' => 15, 'scale' => 4, 'default' => 0.0000])
            ->addColumn('promote_start', 'datetime', ['null' => true])
            ->addColumn('promote_end', 'datetime', ['null' => true])
            ->addColumn('promote', 'boolean', ['default' => 0, 'signed' => false])
            ->addColumn('promote_position', 'integer', ['default' => 0, 'length' => 11, 'signed' => false])
            ->addColumn('promote_new_start', 'datetime', ['null' => true])
            ->addColumn('promote_new_end', 'datetime', ['null' => true])
            ->addColumn('promote_new', 'boolean', ['default' => 0, 'signed' => false])
            ->addColumn('promote_new_position', 'integer', ['default' => 0, 'length' => 11, 'signed' => false])
            ->addColumn('status', 'boolean', ['default' => 0, 'signed' => false])
            ->addColumn('view_counter', 'integer', ['default' => 0, 'limit' => 11, 'signed' => false])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('created_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addColumn('modified_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('deleted', 'datetime', ['null' => true])
            ->addColumn('deleted_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addIndex(['product_type_id'])
            ->addIndex(['foreign_key'])
            ->create();
    }
}
