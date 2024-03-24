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
 * Class UsersSeed
 */
class UsersSeed extends AbstractSeed
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
                'role_id' => 1,
                'locale_id' => 1,
                'uuid_id' => Text::uuid(),
                'foreign_key' => NULL,
                'username' => 'Admin',
                'password' => '$2y$10$ayGbgflXWyMcE35wYplEPeimFgLT4nv1e9WbDN5rbbzLdRTD4qSsu',
                'name' => 'Admin',
                'email' => 'admin@bechlem-connect-light.tld',
                'status' => 1,
                'token' => '91960e62-e46d-11e6-b6ac-c9d3c86fd2cf',
                'activation_date' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'last_login' => NULL,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
            [
                'id' => 2,
                'role_id' => 2,
                'locale_id' => 1,
                'uuid_id' => Text::uuid(),
                'foreign_key' => NULL,
                'username' => 'Rest',
                'password' => '$2y$10$oEPBiaB43JT97OCpE6Vw1O4rD/zqSiioC4rMo38MkM2GAT2W9dO7m',
                'name' => 'Rest',
                'email' => 'rest@bechlem-connect-light.tld',
                'status' => 1,
                'token' => '91961150-e46d-11e6-b6ac-c9d3c86fd2cf',
                'activation_date' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'last_login' => NULL,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
            [
                'id' => 3,
                'role_id' => 3,
                'locale_id' => 1,
                'uuid_id' => Text::uuid(),
                'foreign_key' => NULL,
                'username' => 'Manager',
                'password' => '$2y$10$TE6mBrySVNqecJykdZDYeOJHa0ayJ/7DBsZw5LX59X5Y9e0hcj2yq',
                'name' => 'Manager',
                'email' => 'manager@bechlem-connect-light.tld',
                'status' => 1,
                'token' => '91961010-e46d-11e6-b6ac-c9d3c86fd2cf',
                'activation_date' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'last_login' => NULL,
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],

        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}
