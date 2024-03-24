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
 * ProductTypeAttributes seed.
 */
class ProductTypeAttributesSeed extends AbstractSeed
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
                'uuid_id' => Text::uuid(),
                'foreign_key' => 'product_name_with_manufacturer',
                'title' => 'Shopify Product - Title',
                'alias' => 'shopify_product_title',
                'type' => 'text',
                'description' => '<p><br></p>',
                'empty_value' => 0,
                'wysiwyg' => 0,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
            [
                'id' => 2,
                'uuid_id' => Text::uuid(),
                'foreign_key' => 'short_description',
                'title' => 'Shopify Product - Body HTML',
                'alias' => 'shopify_product_body_html',
                'type' => 'text',
                'description' => '<p><br></p>',
                'empty_value' => 0,
                'wysiwyg' => 0,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
            [
                'id' => 3,
                'uuid_id' => Text::uuid(),
                'foreign_key' => 'manufacturer_name',
                'title' => 'Shopify Product - Vendor',
                'alias' => 'shopify_product_vendor',
                'type' => 'string',
                'description' => '<p><br></p>',
                'empty_value' => 0,
                'wysiwyg' => 0,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
            [
                'id' => 4,
                'uuid_id' => Text::uuid(),
                'foreign_key' => 'product_type_name',
                'title' => 'Shopify Product - Product Type',
                'alias' => 'shopify_product_product_type',
                'type' => 'string',
                'description' => '<p><br></p>',
                'empty_value' => 0,
                'wysiwyg' => 0,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
            [
                'id' => 5,
                'uuid_id' => Text::uuid(),
                'foreign_key' => 'image',
                'title' => 'Shopify Product - Image',
                'alias' => 'shopify_product_image',
                'type' => 'text',
                'description' => '<p><br></p>',
                'empty_value' => 1,
                'wysiwyg' => 0,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
            [
                'id' => 6,
                'uuid_id' => Text::uuid(),
                'foreign_key' => NULL,
                'title' => 'Shopify Product - Handle',
                'alias' => 'shopify_product_handle',
                'type' => 'text',
                'description' => '<p><br></p>',
                'empty_value' => 0,
                'wysiwyg' => 0,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
            [
                'id' => 7,
                'uuid_id' => Text::uuid(),
                'foreign_key' => NULL,
                'title' => 'Shopify Product - Status',
                'alias' => 'shopify_product_status',
                'type' => 'select',
                'description' => '<p><br></p>',
                'empty_value' => 0,
                'wysiwyg' => 0,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
        ];

        $table = $this->table('product_type_attributes');
        $table->insert($data)->save();
    }
}
