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
use Cake\I18n\DateTime;
use Cake\Utility\Text;
use Migrations\AbstractSeed;

/**
 * Products seed.
 */
class ProductsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $dateTime = DateTime::now();

        $data = [
            [
                'id' => 1,
                'product_type_id' => 3,
                'product_condition_id' => 1,
                'product_delivery_time_id' => 2,
                'product_manufacturer_id' => 17,
                'product_tax_class_id' => 2,
                'uuid_id' => Text::uuid(),
                'foreign_key' => '50001415',
                'employee_key' => NULL,
                'manufacturer_key' => '20000264',
                'manufacturer_name' => 'Canon',
                'manufacturer_sku' => '1557A003,1557A003AA,1557A003BA',
                'category_key' => '145211100',
                'category_name' => 'Toner black',
                'sku' => '1557 A 003',
                'ean' => '4960999830353,8714574981338',
                'name' => 'Canon FX3,CRGFX3,EPFX3 Toner cartridge black - 1557A003,1557A003AA,1557A003BA',
                'slug' => 'canon-fx3-CRGFX3-EPFX3-toner-cartridge-black-1557A003-1557A003AA-1557A003BA',
                'stock' => '100.0000',
                'price' => '10.0000',
                'promote_start' => $dateTime->i18nFormat('yyyy-MM-dd 00:00:00'),
                'promote_end' => NULL,
                'promote' => 1,
                'promote_position' => 1,
                'promote_new_start' => $dateTime->i18nFormat('yyyy-MM-dd 00:00:00'),
                'promote_new_end' => NULL,
                'promote_new' => 1,
                'promote_new_position' => 1,
                'status' => 1,
                'view_counter' => 0,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
        ];

        $table = $this->table('products');
        $table->insert($data)->save();
    }
}
