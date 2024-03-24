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

class InitialRegistrations extends AbstractMigration
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
        // Table registrations
        $tableName = 'registrations';

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
            ->addColumn('registration_type_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('uuid_id', 'uuid', ['null' => true])
            ->addColumn('billing_name', 'string', ['limit' => 255])
            ->addColumn('billing_name_addition', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('billing_legal_form', 'string', ['limit' => 255])
            ->addColumn('billing_vat_number', 'string', ['limit' => 255])
            ->addColumn('billing_salutation', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('billing_first_name', 'string', ['limit' => 255])
            ->addColumn('billing_middle_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('billing_last_name', 'string', ['limit' => 255])
            ->addColumn('billing_management', 'string', ['limit' => 255])
            ->addColumn('billing_email', 'string', ['limit' => 255])
            ->addColumn('billing_website', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('billing_telephone', 'string', ['limit' => 255])
            ->addColumn('billing_mobilephone', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('billing_fax', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('billing_street', 'string', ['limit' => 255])
            ->addColumn('billing_street_addition', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('billing_postcode', 'string', ['limit' => 255])
            ->addColumn('billing_city', 'string', ['limit' => 255])
            ->addColumn('billing_country', 'string', ['limit' => 255])
            ->addColumn('shipping_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_name_addition', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_management', 'string', ['limit' => 255])
            ->addColumn('shipping_email', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_telephone', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_mobilephone', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_fax', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_street', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_street_addition', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_postcode', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_city', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('shipping_country', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('newsletter_email', 'string', ['limit' => 255])
            ->addColumn('remark', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('register_excerpt', 'string', ['limit' => 255])
            ->addColumn('newsletter', 'boolean', ['default' => 0, 'signed' => false])
            ->addColumn('marketing', 'boolean', ['default' => 0, 'signed' => false])
            ->addColumn('terms_conditions', 'boolean', ['default' => 0, 'signed' => false])
            ->addColumn('privacy_policy', 'boolean', ['default' => 0, 'signed' => false])
            ->addColumn('ip', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('created_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addColumn('modified_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addColumn('deleted', 'datetime', ['null' => true])
            ->addColumn('deleted_by', 'integer', ['default' => null, 'limit' => 11, 'null' => true])
            ->addIndex(['registration_type_id'])
            ->addIndex(['billing_name'])
            ->addIndex(['billing_email'])
            ->create();
    }
    // @codingStandardsIgnoreEnd
}
