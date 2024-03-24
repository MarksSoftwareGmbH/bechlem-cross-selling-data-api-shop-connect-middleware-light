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
 * Class InitialUserProfiles
 */
class InitialUserProfiles extends AbstractMigration
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
        // Table user_profiles
        $tableName = 'user_profiles';

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
            ->addColumn('user_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('uuid_id', 'uuid', ['null' => true])
            ->addColumn('foreign_key', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('prefix', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('salutation', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('suffix', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('first_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('middle_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('last_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('gender', 'enum', ['values' => ['Male', 'Female'], 'null' => true])
            ->addColumn('birthday', 'date', ['null' => true])
            ->addColumn('website', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('telephone', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('mobilephone', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('fax', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('company', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('street', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('street_addition', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('postcode', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('city', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('country_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('about_me', 'text', ['null' => true])
            ->addColumn('tags', 'text', ['null' => true])
            ->addColumn('timezone', 'string', ['default' => 'Europe/Berlin', 'limit' => 255])
            ->addColumn('image', 'text', ['null' => true])
            ->addColumn('view_counter', 'integer', ['default' => 0, 'limit' => 11, 'signed' => false])
            ->addColumn('status', 'boolean', ['default' => 0, 'signed' => false])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('created_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addColumn('modified_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('deleted', 'datetime', ['null' => true])
            ->addColumn('deleted_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addIndex(['user_id'])
            ->create();

    }
    // @codingStandardsIgnoreEnd
}
