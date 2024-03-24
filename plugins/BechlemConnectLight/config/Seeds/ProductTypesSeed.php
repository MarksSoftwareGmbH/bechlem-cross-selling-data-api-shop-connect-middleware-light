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
 * ProductTypes seed.
 */
class ProductTypesSeed extends AbstractSeed
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
                'foreign_key' => NULL,
                'title' => 'Default',
                'alias' => 'default',
                'description' => '<p>This is the "Default" BECHLEM CONNECT LIGHT product type.</p>',
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
                'foreign_key' => NULL,
                'title' => 'Custom',
                'alias' => 'custom',
                'description' => '<p>This is a "Custom" product type for RESTful API purposes.<br></p>',
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
                'foreign_key' => NULL,
                'title' => 'Shopify',
                'alias' => 'shopify',
                'description' => '<h1><a href="https://shopify.dev/docs/api/admin-rest" target="_blank">REST Admin API reference</a></h1><p>The Admin API lets you build apps and integrations that extend and enhance the Shopify admin.</p><h2><span>Authentication</span></h2><div><p>All REST Admin API queries require a valid Shopify access token.</p>
<p>Public and custom apps created in the Partner Dashboard generate tokens using <a href="https://shopify.dev/apps/auth/oauth">OAuth</a>, and custom apps made in the Shopify admin are <a href="https://shopify.dev/apps/auth/admin-app-access-tokens">authenticated in the Shopify admin</a>. To simplify the authentication process, use one of the recommended Shopify client libraries.</p>
<p>Include your token as a <code>X-Shopify-Access-Token</code> header on all API queries. Using Shopify’s supported <a href="https://shopify.dev/apps/tools/api-libraries">client libraries</a> can simplify this process.</p>
<p>To keep the platform secure, apps need to request specific <a href="https://shopify.dev/api/usage/access-scopes">access scopes</a> during the install process. Only request as much data access as your app needs to work.</p>
<p>Learn more about <a href="https://shopify.dev/apps/auth">getting started with authentication</a> and <a href="https://shopify.dev/apps/getting-started">building apps</a>.</p><h2><span>Endpoints and requests</span></h2><div><p>Admin REST API endpoints are organized by resource type. You’ll need to use different endpoints depending on your app’s requirements.</p>
<p>All Admin REST API endpoints follow this pattern:</p>
<p><code><span>https://{store<wbr>_name}.myshopify.com<wbr>/admin<wbr>/api<wbr>/2024-01<wbr>/{resource}.json</span></code></p></div><div><div><div></div></div></div><div><div id="section-59127408" role="region" aria-labelledby="endpoints-post" style="max-height: 0px; overflow: hidden;"></div></div><div><div></div></div><div><div id="section-44789135" role="region" aria-labelledby="endpoints-get" style="max-height: 0px; overflow: hidden;"></div></div><div><div></div></div><div><div id="section-30833888" role="region" aria-labelledby="endpoints-put" style="max-height: 0px; overflow: hidden;"></div></div><div><div></div></div><div><div><div id="section-55262498" role="region" aria-labelledby="endpoints-delete" style="max-height: 0px; overflow: hidden;"></div></div></div><div><p>The Admin API is versioned, with new releases four times per year. To keep your app stable, make sure you specify a supported version in the URL. Learn more about <a href="https://shopify.dev/api/usage/versioning">API versioning</a>.</p>
<p>All REST endpoints support <a href="https://shopify.dev/api/usage/pagination-rest">cursor-based pagination</a>. All requests produce HTTP <a href="https://shopify.dev/api/usage/response-codes">response status codes</a>.</p>
<p>Learn more about <a href="https://shopify.dev/api/usage">API usage</a>.</p><h2><span>Rate limits</span></h2><p>The REST Admin API supports a limit of 40 requests per app per store per minute. This allotment replenishes at a rate of 2 requests per second. The rate limit is increased by a factor of 10 for Shopify Plus stores.</p><p></p></div><p></p></div><p></p><p></p>',
                'created' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'created_by' => 1,
                'modified' => $dateTime->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified_by' => 1,
                'deleted' => NULL,
                'deleted_by' => NULL,
            ],
        ];

        $table = $this->table('product_types');
        $table->insert($data)->save();
    }
}
