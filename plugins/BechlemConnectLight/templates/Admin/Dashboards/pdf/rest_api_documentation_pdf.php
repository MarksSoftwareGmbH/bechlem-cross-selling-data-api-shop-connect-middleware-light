<?php

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
use Cake\Core\Configure;

$backendBoxColor = 'secondary';
if (Configure::check('BechlemConnectLight.settings.backendBoxColor')):
    $backendBoxColor = Configure::read('BechlemConnectLight.settings.backendBoxColor');
endif;

// Set http host by environment
if (filter_var(env('HTTP_HOST'), FILTER_VALIDATE_IP) !== false):
    $httpHost = env('HTTP_X_FORWARDED_HOST');
else:
    $httpHost = env('HTTP_HOST');
endif;

/*
 * When using proxies or load balancers, SSL/TLS connections might
 * get terminated before reaching the server. If you trust the proxy,
 * you can enable `$trustProxy` to rely on the `X-Forwarded-Proto`
 * header to determine whether to generate URLs using `https`.
 *
 * See also https://book.cakephp.org/4/en/controllers/request-response.html#trusting-proxy-headers
 */
$trustProxy = false;
$s = null;
if (env('HTTPS') || ($trustProxy && env('HTTP_X_FORWARDED_PROTO') === 'https')):
    $s = 's';
endif;
?>
<div>
    <div>
        <h1><?= __d('bechlem_connect_light', 'REST API documentation'); ?></h1><br />
        <strong><?= __d('bechlem_connect_light', 'The REST API lets you build apps and integrations that extend and enhance the BECHLEM CONNECT "LIGHT" application and middleware.'); ?></strong><br />
        <br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'REST API user'); ?></strong><br />
        <br />
        <?= __d(
            'bechlem_connect_light',
            'First you need to create a {user} with the {role} "{rest}" for the authentication request.',
            [
                'user' => $this->Html->link(
                    __d('bechlem_connect_light', 'User'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Users',
                        'action'        => 'index',
                    ],
                    [
                        'style'         => 'color: #941342;',
                        'target'        => '_self',
                        'title'         => __d('bechlem_connect_light', 'User'),
                        'escape'        => false,
                    ]),
                'role' => $this->Html->link(
                    __d('bechlem_connect_light', 'Role'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Roles',
                        'action'        => 'index',
                    ],
                    [
                        'style'         => 'color: #941342;',
                        'target'        => '_self',
                        'title'         => __d('bechlem_connect_light', 'Role'),
                        'escape'        => false,
                    ]),
                'rest' => $this->Html->link(
                    __d('bechlem_connect_light', 'Rest'),
                    [
                        'plugin'        => 'BechlemConnectLight',
                        'controller'    => 'Roles',
                        'action'        => 'index',
                        '?'             => ['search' => 'Rest'],
                    ],
                    [
                        'style'         => 'color: #941342;',
                        'target'        => '_self',
                        'title'         => __d('bechlem_connect_light', 'Rest'),
                        'escape'        => false,
                    ]),
            ]); ?><br />
        <br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Authentication'); ?></strong><br />
        <br />
        <?= __d(
            'bechlem_connect_light',
            'All REST API queries require a valid {jwt}.',
            [
                'jwt' =>  $this->Html->link(
                    'JSON Web Token (JWT)',
                    'https://jwt.io/',
                    [
                        'style'     => 'color: #941342;',
                        'target'    => '_blank',
                        'title'     => 'JSON Web Token (JWT)',
                        'escape'    => false,
                    ])
            ]); ?><br />
            <?= __d(
                'bechlem_connect_light',
                'This is an open standard ({rfc7519}) that defines a compact and self-contained way for securely transmitting information between parties as a JSON object.',
                [
                    'rfc7519' =>  $this->Html->link(
                        'RFC 7519',
                        'https://datatracker.ietf.org/doc/html/rfc7519',
                        [
                            'style'     => 'color: #941342;',
                            'target'    => '_blank',
                            'title'     => 'RFC 7519',
                            'escape'    => false,
                        ])
                ]); ?><br />
            <br />
            <?= __d('bechlem_connect_light', 'Second you need to request a Bearer token with the created Rest user via username:password through a POST method.'); ?><br />
            <?= __d('bechlem_connect_light', 'Whenever the user wants to access a protected route or resource, the user agent should send the JWT, typically in the Authorization header using the Bearer schema. The content of the header should look like the following:'); ?>
            <small><pre><code class="language-php">Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIsImV4cCI6MTcxMjE3MTQ4MX0.Rz_EzzJu0R3fjyq0vz_aLIEAOCDNLHya1magyhc-zgk</code></pre></small><br />
            <br />
            <?= __d('bechlem_connect_light', 'Include your token as a Bearer-Token header on all API queries.'); ?><br />
            <?= __d('bechlem_connect_light', 'To keep the platform secure, apps need to request specific access scopes during the process.'); ?><br />
            <?= __d('bechlem_connect_light', 'Only request as much data access as your app needs to work.'); ?><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/oauth/token</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIsImV4cCI6MTcxMjE3MTQ4MX0.Rz_EzzJu0R3fjyq0vz_aLIEAOCDNLHya1magyhc-zgk",
        "expires": 1712171481
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Code snippet'); ?></strong><br />
        <br />
        C - libcurl
        <small><pre><code class="language-php">
CURL *curl;
CURLcode res;
curl = curl_easy_init();
if(curl) {
    curl_easy_setopt(curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_easy_setopt(curl, CURLOPT_URL, "<?= 'http' . h($s) . '://' . h($httpHost); ?>/api/oauth/token");
    curl_easy_setopt(curl, CURLOPT_FOLLOWLOCATION, 1L);
    curl_easy_setopt(curl, CURLOPT_DEFAULT_PROTOCOL, "https");
    struct curl_slist *headers = NULL;
    headers = curl_slist_append(headers, "Content-Type: application/x-www-form-urlencoded");
    headers = curl_slist_append(headers, "Cookie: bechlem_connect_light=kjugdj8iisvptfrjq939ts9va8");
    curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);
    const char *data = "username=Rest&password=superrest";
    curl_easy_setopt(curl, CURLOPT_POSTFIELDS, data);
    res = curl_easy_perform(curl);
    curl_slist_free_all(headers);
}
curl_easy_cleanup(curl);
        </code></pre></small><br />
        <br />
        C# - HttpClient
        <small><pre><code class="language-php">
var client = new HttpClient();
var request = new HttpRequestMessage(HttpMethod.Post, "<?= 'http' . h($s) . '://' . h($httpHost); ?>/api/oauth/token");
request.Headers.Add("Cookie", "bechlem_connect_light=kjugdj8iisvptfrjq939ts9va8");
var collection = new List<KeyValuePair<string, string>>();
collection.Add(new("username", "Rest"));
collection.Add(new("password", "superrest"));
var content = new FormUrlEncodedContent(collection);
request.Content = content;
var response = await client.SendAsync(request);
response.EnsureSuccessStatusCode();
Console.WriteLine(await response.Content.ReadAsStringAsync());
        </code></pre></small><br />
        <br />
        PHP - cURL
        <small><pre><code class="language-php">
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => '<?= 'http' . h($s) . '://' . h($httpHost); ?>/api/oauth/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'username=Rest&password=superrest',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: bechlem_connect_light=kjugdj8iisvptfrjq939ts9va8'
    ),
));
$response = curl_exec($curl);
curl_close($curl);
echo $response;
        </code></pre></small><br />
        <br />
        Python - http.client
        <small><pre><code class="language-php">
import http.client
conn = http.client.HTTPSConnection("<?= 'http' . h($s) . '://' . h($httpHost); ?>")
payload = 'username=Rest&password=superrest'
headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'Cookie': 'bechlem_connect_light=kjugdj8iisvptfrjq939ts9va8'
}
conn.request("POST", "/api/oauth/token", payload, headers)
res = conn.getresponse()
data = res.read()
print(data.decode("utf-8"))
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Users'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/users</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/users/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "role_id": 1,
        "locale_id": 2,
        "uuid_id": "8a9227d9-c9cb-479b-bf88-c41f3ef63f6d",
        "foreign_key": "",
        "username": "Admin",
        "name": "Admin",
        "email": "admin@bechlem-connect-light.tld",
        "status": true,
        "activation_date": "2024-03-27T14:19:16+00:00",
        "last_login": "2024-04-03T13:58:17+00:00",
        "created": "2024-03-27T14:19:16+00:00",
        "created_by": 1,
        "modified": "2024-04-03T13:58:17+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "name_username": "Admin - Admin",
        "name_username_email": "Admin - Admin  (admin@bechlem-connect-light.tld)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'User profiles'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/user-profiles</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/user-profiles/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "user_id": 1,
        "uuid_id": "be86a9cd-d7b5-455b-842d-0952c03550af",
        "foreign_key": "22b2c222-57b9-4544-b326-96c951c1a957",
        "prefix": "",
        "salutation": "Mr.",
        "suffix": null,
        "first_name": "Marks",
        "middle_name": "",
        "last_name": "Software",
        "gender": "Male",
        "birthday": null,
        "website": "https:\/\/www.marks-software.de\/",
        "telephone": "",
        "mobilephone": "",
        "fax": "",
        "company": "Marks Software GmbH",
        "street": "Holunderweg",
        "street_addition": "20",
        "postcode": "29664",
        "city": "Walsrode",
        "country_id": 98,
        "about_me": "Coder in Chef",
        "tags": "",
        "timezone": "Europe\/Berlin",
        "image": "\/bechlem_connect_light\/img\/avatars\/marks_software_gmbh.jpg",
        "view_counter": 0,
        "status": true,
        "created": "2024-03-27T14:19:16+00:00",
        "created_by": 1,
        "modified": "2024-03-27T14:19:16+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "full_name": "Marks Software"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Roles'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/roles</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/roles/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "d029f2b3-1849-42a4-9365-87ede4f8b547",
        "foreign_key": null,
        "title": "Admin",
        "alias": "admin",
        "created": "2024-03-27T14:19:16+00:00",
        "created_by": 1,
        "modified": "2024-03-27T14:19:16+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "title_alias": "Admin (admin)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Registrations'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/registrations</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/registrations/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "registration_type_id": 1,
        "uuid_id": "a029f2b3-1849-42a4-9365-87ede4f8b517",
        "billing_name": "Marks Software GmbH",
        "billing_name_addition": "",
        "billing_legal_form": "GmbH",
        "billing_vat_number": "DE315790961",
        "billing_salutation": "Mr.",
        "billing_first_name": "Lukas",
        "billing_middle_name": "Siegmund",
        "billing_last_name": "Marks",
        "billing_management": "Lukas Marks",
        "billing_email": "info@marks-software.de",
        "billing_website": "https:\/\/www.marks-software.de\/de",
        "billing_telephone": "+4951617875356",
        "billing_mobilephone": "",
        "billing_fax": "",
        "billing_street": "Holunderweg",
        "billing_street_addition": "20",
        "billing_postcode": "29664",
        "billing_city": "Walsrode",
        "billing_country": "Deutschland",
        "shipping_name": "Marks Software GmbH",
        "shipping_name_addition": "",
        "shipping_management": "Lukas Marks",
        "shipping_email": "info@marks-software.de",
        "shipping_telephone": "+4951617875356",
        "shipping_mobilephone": "",
        "shipping_fax": "",
        "shipping_street": "Holunderweg",
        "shipping_street_addition": "20",
        "shipping_postcode": "29664",
        "shipping_city": "Walsrode",
        "shipping_country": "Deutschland",
        "newsletter_email": "info@marks-software.de",
        "remark": "",
        "register_excerpt": "register.pdf",
        "newsletter": true,
        "marketing": true,
        "terms_conditions": true,
        "privacy_policy": true,
        "ip": "127.0.0.1",
        "created": "2024-04-03T20:56:14+00:00",
        "created_by": 1,
        "modified": "2024-04-03T20:56:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "full_billing_name": "",
        "full_shipping_name": "",
        "full_billing_street": "Holunderweg 20",
        "full_shipping_street": "Holunderweg 20",
        "full_billing_address": "Holunderweg 20, 29664 Walsrode",
        "full_shipping_address": "Holunderweg 20, 29664 Walsrode"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Registration types'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/registration-types</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/registration-types/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "ca29f2b3-1849-42a4-9365-87ede4f8b711",
        "foreign_key": "",
        "title": "Default",
        "alias": "default",
        "description": "\u003Cp\u003EDefault registration type.\u003Cbr\u003E\u003C\/p\u003E",
        "created": "2024-04-03T20:51:24+00:00",
        "created_by": 1,
        "modified": "2024-04-03T20:51:24+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "title_alias": "Default (default)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Domains'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/domains</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/domains/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "ca43008a-1256-4020-b063-199d0c09768c",
        "scheme": "http",
        "url": "bechlem-connect-light-github.localhost.local",
        "name": "Bechlem Connect Light",
        "theme": "BechlemConnectLight",
        "created": "2024-03-27T14:19:16+00:00",
        "created_by": 1,
        "modified": "2024-03-27T14:19:16+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "name_id": "Bechlem Connect Light (1)",
        "name_scheme": "Bechlem Connect Light (http)",
        "name_url": "Bechlem Connect Light (bechlem-connect-light-github.localhost.local)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Locales'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/locales</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/locales/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "613e5684-a914-40cd-88b4-516a22e4420b",
        "foreign_key": null,
        "name": "English",
        "native": "English",
        "code": "en_US",
        "weight": 1,
        "status": true,
        "created": "2024-03-27T14:19:16+00:00",
        "created_by": 1,
        "modified": "2024-03-27T14:19:16+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Countries'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/countries</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/countries/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "5d4ad19a-d02f-44a1-aad2-69c3cfb43d47",
        "foreign_key": "AF",
        "name": "Afghanistan",
        "slug": "afghanistan",
        "code": "AF",
        "info": "",
        "locale": "de_DE",
        "locale_translation": "Afghanistan",
        "status": true,
        "created": "2024-03-27T14:19:16+00:00",
        "created_by": 1,
        "modified": "2024-03-27T14:19:16+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "name_id": "Afghanistan (1)",
        "name_foreign_key": "Afghanistan (AF)",
        "name_code": "Afghanistan (AF)",
        "locale_translation_id": "Afghanistan (1)",
        "locale_translation_foreign_key": "Afghanistan (AF)",
        "locale_translation_code": "Afghanistan (AF)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Logs'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/logs</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/logs/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": null,
        "request": "POST",
        "type": "httpCall",
        "message": "https://www.datawriter.de/api/v12/index.php?table=reseller",
        "ip": "31.17.244.18",
        "uri": "/admin/bechlem-resellers/update-all",
        "data": "[{\"idreseller\":\"Bechlem Demo\",\"text\":\"Bechlem CrossSellingData Demo\",\"nzpicture\":\"\"}]",
        "created": "2024-04-05T17:50:20+00:00",
        "created_by": 1,
        "modified": "2024-04-05T17:50:20+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Settings'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/settings</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/settings/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "domain_id": 1,
        "uuid_id": "ab18788c-7618-4c0f-9bac-f148bf7c4d29",
        "foreign_key": null,
        "parameter": "theme",
        "name": "backendTheme",
        "value": "BechlemConnectLight",
        "title": "Backend Theme",
        "description": "Detault backend theme",
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem Connect'); ?> <?= __d('bechlem_connect_light', 'Configs'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-connect-configs</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-connect-configs/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "9f3951b4-f2df-4234-832e-9cb511eadfc3",
        "title": "Datawriter",
        "alias": "datawriter",
        "description": "Dock to Europe`s most comprehensive printer supplies database.",
        "host": "www.datawriter.de",
        "port": "443",
        "scheme": "https",
        "username": "www.datawriter.de",
        "password": "www.datawriter.de",
        "status": 1,
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem Connect'); ?> <?= __d('bechlem_connect_light', 'Requests'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-connect-requests</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-connect-requests/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "bechlem_connect_config_id": 1,
        "uuid_id": "7fde574f-5aa7-4713-b2e7-dcea8839897d",
        "name": "Brand",
        "slug": "brand",
        "method": "GET",
        "url": "/api/v12/index.php",
        "data": "brand",
        "language": null,
        "options": null,
        "description": "All brand names, original and alternative (Simple table based on Bechlem API V1.2)",
        "example": null,
        "log": 0,
        "status": 1,
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "name_id": "Brand (1)",
        "name_method": "Brand (GET)",
        "name_url": "Brand (/api/v12/index.php)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Brands'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-brands</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-brands/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "da242634-cf30-4879-bdac-5467f1fe9473",
        "id_brand": "20002769",
        "name": "Sindoh",
        "upper_name": "SINDOH",
        "id_par_brand": "",
        "importance": "2",
        "type": "OEM",
        "created": "2024-04-03T21:53:44+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:53:44+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Categories'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-categories</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-categories/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "783bac56-2086-4334-9538-09750f1dd988",
        "id_category": "145269000",
        "name": "Sonstige",
        "language": "de",
        "created": "2024-04-03T21:54:22+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:54:22+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Identifiers'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-identifiers</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-identifiers/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "9312c42a-3875-4ae8-b971-d3961138c033",
        "id_item": "50048616",
        "id_position": "2",
        "id_type": "PARTNR",
        "id_identifier": "PGI-520 BK",
        "sync_id": "PGI520BK",
        "created": "2024-04-03T21:54:26+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:54:26+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Printers'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-printers</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-printers/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "65ddb56e-af7b-4ef5-839e-4d78450f55f1",
        "id_item": "40054978",
        "id_brand": "20000264",
        "brand": "Canon",
        "art_nr": "",
        "name": "S 800",
        "id_category": "145131000",
        "category": "InkJetdrucker",
        "printer_series": "S",
        "ean": "",
        "picture": "",
        "language": "de",
        "created": "2024-04-03T21:54:32+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:54:32+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Printer serieses'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-printer-serieses</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-printer-serieses/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "5bd23e12-2a2d-40ab-94b3-6e0dd91b5b8b",
        "id_printer_series": "34000666",
        "id_brand": "20000984",
        "brand": "Kyocera",
        "name": "FS",
        "created": "2024-04-03T21:54:36+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:54:36+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Printer to supplies'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-printer-to-supplies</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-printer-to-supplies/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "22e214ab-77af-487a-8224-2a487d723be6",
        "id_item_printer": "50079096",
        "id_item_supply": "50093943",
        "created": "2024-04-03T21:54:45+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:54:45+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Products'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-products</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-products/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "6b1b5fc5-f1a4-41c1-ae05-6870f2017c0c",
        "bechlem_id": "50043547",
        "ean": "4012700361707,4018474361707,4018474101280",
        "manufacturer_sku": "361707",
        "your_sku": "361707",
        "manufacturer_id": "20001109",
        "manufacturer_name": "Pelikan",
        "product_name_with_manufacturer": "Pelikan C28 Tintenpatrone cyan - 361707",
        "short_description": "Pelikan 361707/C28 Tintenpatrone cyan, 1.365 Seiten 13ml (ersetzt Canon CLI-8C)",
        "product_type_id": "145222210",
        "product_type_name": "Tintenpatrone cyan",
        "image": "35291",
        "created": "2024-04-03T21:55:45+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:55:45+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Product accessories'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-product-accessories</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-product-accessories/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "bechlem_product_id": "50079096",
        "uuid_id": "e54efe8a-e4e4-4e36-822f-11f5d159b055",
        "referenced_product_id": "50093943",
        "type": "Printer",
        "created": "2024-04-03T21:55:34+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:55:34+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Resellers'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-resellers</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-resellers/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "1257812a-a630-46de-99b8-7c9c7b5a3639",
        "id_reseller": "Bechlem Demo",
        "name": "Bechlem CrossSellingData Demo",
        "picture": "",
        "created": "2024-04-05T17:50:20+00:00",
        "created_by": null,
        "modified": "2024-04-05T17:50:20+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Reseller items'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-reseller-items</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-reseller-items/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "2ff35902-dc81-466e-8b93-e9b482fdb32b",
        "id_art_nr": "361707",
        "id_reseller": "Bechlem Demo",
        "id_item": "50043547",
        "ean": "4012700361707",
        "oem_nr": "361707",
        "description": "Pelikan 361707/C28 Tintenpatrone cyan, 1.365 Seiten 13ml (ersetzt Canon CLI-8C)",
        "ve": "",
        "language": "de",
        "created": "2024-04-03T21:54:56+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:54:56+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Supplies'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-supplies</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-supplies/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "2daa6ac5-4736-4b60-8a7b-cc332ecaed63",
        "id_item": "50054804",
        "id_brand": "20000264",
        "brand": "Canon",
        "art_nr": "2934 B 010",
        "part_nr": "CLI-521",
        "name": "Tintenpatrone MultiPack C,M,Y",
        "id_category": "145222400",
        "category": "Tintenpatrone MultiPack",
        "color": "cyan magenta yellow",
        "is_compatible": "F",
        "ve": "3",
        "yield": "446",
        "coverage": "",
        "measures": "",
        "content": "446 Seiten, 9 ml, VE=3",
        "content_ml": "9",
        "content_gram": "",
        "content_char": "",
        "german_group_no": "",
        "supply_series": "CLI-521",
        "ean": "8714574525808",
        "picture": "66909",
        "language": "de",
        "created": "2024-04-03T21:55:01+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:55:01+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Supply serieses'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-supply-serieses</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-supply-serieses/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "d4a9dab6-6bf4-4b99-bfbb-f297535da000",
        "id_supply_series": "90000873",
        "id_brand": "20000264",
        "brand": "Canon",
        "name": "BCI-5",
        "created": "2024-04-03T21:55:08+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:55:08+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Supply to oem references'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-supply-to-oem-references</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-supply-to-oem-references/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "3574fcdc-c11d-4ab4-8fed-2cf1857a28a9",
        "id_item_supply": "50074214",
        "id_item_supply_oem": "50054804",
        "id_brand": "20000264",
        "brand": "Canon",
        "art_nr": "2934 B 010",
        "part_nr": "CLI-521",
        "yield": "446",
        "content_ml": "9",
        "created": "2024-04-03T21:55:13+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:55:13+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Bechlem'); ?> <?= __d('bechlem_connect_light', 'Supply to supplies'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-supply-to-supplies</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/bechlem-supply-to-supplies/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "7b4887bb-3b65-4146-962e-a7bf3159b822",
        "id_item_supply": "50027092",
        "id_item_supply_2": "50036945",
        "created": "2024-04-03T21:55:22+00:00",
        "created_by": null,
        "modified": "2024-04-03T21:55:22+00:00",
        "modified_by": null,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product brands'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-brands</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-brands/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "abcd87bb-3b65-4146-962e-a7bf3159b111",
        "foreign_key": "3m-ace",
        "name": "ACE™",
        "slug": "ace",
        "website": "https://www.acebrand.com/3M/en_US/ace-brand/",
        "description": "",
        "image": "",
        "logo": "https://multimedia.3m.com/mws/media/1988027O/ace-brand-logo.png",
        "status": 1,
        "created": "2024-04-06T16:17:24+00:00",
        "created_by": 1,
        "modified": "2024-04-06T16:20:04+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "name_foreign_key": ""
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product categories'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-categories</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-categories/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "parent_id": null,
        "uuid_id": "c81c9982-65e1-48f2-9a0d-ddb4d69a70ab",
        "foreign_key": "145333000",
        "lft": 1,
        "rght": 2,
        "name": "10x15",
        "slug": "10x15",
        "description": "",
        "background_image": "",
        "meta_description": "",
        "meta_keywords": "",
        "locale": "en_US",
        "status": 1,
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "name_foreign_key": "10x15 (145333000)",
        "name_locale": "10x15 (en_US)",
        "name_slug_locale": "10x15 (10x15) (en_US)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product conditions'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-conditions</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-conditions/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "4ed8be6c-c046-4dab-b3dc-76f4083af633",
        "foreign_key": null,
        "title": "New",
        "alias": "new",
        "description": "<p>New product.</p>",
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "title_foreign_key": "",
        "title_alias": "New (new)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product delivery times'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-delivery-times</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-delivery-times/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "74a9f009-034e-44b9-9bcb-3108494c999d",
        "foreign_key": null,
        "title": "1 Day",
        "alias": "1-day",
        "description": "<p>Logisticians may vary.</p>",
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "title_foreign_key": "",
        "title_alias": "1 Day (1-day)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product intrastat codes'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-intrastat-codes</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-intrastat-codes/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": null,
        "foreign_key": "",
        "code": "8443.31.00",
        "type": "KERNREAKTOREN, KESSEL, MASCHINEN, APPARATE UND MECHANISCHE GERÄTE; TEILE DAVON",
        "name": "Drucker, Kopiergeräte und Fernkopierer, auch miteinander kombiniert",
        "description": "Maschinen, die mindestens zwei der Funktionen Drucken, Kopieren oder Übertragen von Fernkopien ausführen und die an eine automatische Datenverarbeitungsmaschine oder ein Netzwerk angeschlossen werden können",
        "created": "2024-04-06T17:17:58+00:00",
        "created_by": 1,
        "modified": "2024-04-06T17:17:58+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product manufacturers'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-manufacturers</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-manufacturers/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "3db2e2fa-43a6-4cb7-a74e-ee7597353806",
        "foreign_key": "20000002",
        "name": "3M",
        "slug": "3m",
        "website": "",
        "description": "OEM",
        "logo": "",
        "image": "",
        "status": 1,
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "name_foreign_key": "3M (20000002)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product type attribute values'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-product-type-attribute-values</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-product-type-attribute-values/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "product_id": 1,
        "product_type_attribute_id": 2,
        "value": "Canon 1557A003/FX-3 Toner cartridge black, 2.700 pages/5%",
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Products'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/products</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/products/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "product_type_id": 3,
        "product_condition_id": 1,
        "product_delivery_time_id": 2,
        "product_manufacturer_id": 17,
        "product_tax_class_id": 2,
        "uuid_id": "42f40b0b-bad3-47e0-b6ac-396028b49f4d",
        "foreign_key": "50001415",
        "employee_key": null,
        "manufacturer_key": "20000264",
        "manufacturer_name": "Canon",
        "manufacturer_sku": "1557A003,1557A003AA,1557A003BA",
        "category_key": "145211100",
        "category_name": "Toner black",
        "sku": "1557 A 003",
        "ean": "4960999830353,8714574981338",
        "name": "Canon FX3,CRGFX3,EPFX3 Toner cartridge black - 1557A003,1557A003AA,1557A003BA",
        "slug": "canon-fx3-CRGFX3-EPFX3-toner-cartridge-black-1557A003-1557A003AA-1557A003BA",
        "stock": "100.0000",
        "price": "10.0000",
        "promote_start": "2024-03-23T00:00:00+00:00",
        "promote_end": null,
        "promote": 1,
        "promote_position": 1,
        "promote_new_start": "2024-03-23T00:00:00+00:00",
        "promote_new_end": null,
        "promote_new": 1,
        "promote_new_position": 1,
        "status": 1,
        "view_counter": 0,
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "product_type": {
            "id": 3,
            "uuid_id": "91a062ae-18ed-4a62-9945-254912629d89",
            "foreign_key": null,
            "title": "Shopify",
            "alias": "shopify",
            "description": "",
            "created": "2024-03-23T00:17:14+00:00",
            "created_by": 1,
            "modified": "2024-03-23T00:17:14+00:00",
            "modified_by": 1,
            "deleted": null,
            "deleted_by": null,
            "product_type_attributes": [
                {
                    "id": 2,
                    "uuid_id": "437df7e9-6e58-41ec-9ca2-b7d53a828769",
                    "foreign_key": "short_description",
                    "title": "Shopify Product - Body HTML",
                    "alias": "shopify_product_body_html",
                    "type": "text",
                    "description": "",
                    "empty_value": 0,
                    "wysiwyg": 0,
                    "created": "2024-03-23T00:17:14+00:00",
                    "created_by": 1,
                    "modified": "2024-03-23T00:17:14+00:00",
                    "modified_by": 1,
                    "deleted": null,
                    "deleted_by": null,
                    "_joinData": {
                        "id": 2,
                        "product_type_id": 3,
                        "product_type_attribute_id": 2,
                        "position": 0
                    },
                    "product_type_attribute_choices": [],
                    "title_foreign_key": "Shopify Product - Body HTML (short_description)",
                    "title_alias": "Shopify Product - Body HTML (shopify_product_body_html)"
                },
                {
                    "id": 6,
                    "uuid_id": "5c05475e-ae3d-4fb9-b3fc-08a00c444ecc",
                    "foreign_key": null,
                    "title": "Shopify Product - Handle",
                    "alias": "shopify_product_handle",
                    "type": "text",
                    "description": "",
                    "empty_value": 0,
                    "wysiwyg": 0,
                    "created": "2024-03-23T00:17:14+00:00",
                    "created_by": 1,
                    "modified": "2024-03-23T00:17:14+00:00",
                    "modified_by": 1,
                    "deleted": null,
                    "deleted_by": null,
                    "_joinData": {
                        "id": 6,
                        "product_type_id": 3,
                        "product_type_attribute_id": 6,
                        "position": 0
                    },
                    "product_type_attribute_choices": [],
                    "title_foreign_key": "",
                    "title_alias": "Shopify Product - Handle (shopify_product_handle)"
                },
                {
                    "id": 5,
                    "uuid_id": "e67f41b1-464a-48fe-992c-b2c3f385af19",
                    "foreign_key": "image",
                    "title": "Shopify Product - Image",
                    "alias": "shopify_product_image",
                    "type": "text",
                    "description": "",
                    "empty_value": 1,
                    "wysiwyg": 0,
                    "created": "2024-03-23T00:17:14+00:00",
                    "created_by": 1,
                    "modified": "2024-03-23T00:17:14+00:00",
                    "modified_by": 1,
                    "deleted": null,
                    "deleted_by": null,
                    "_joinData": {
                        "id": 5,
                        "product_type_id": 3,
                        "product_type_attribute_id": 5,
                        "position": 0
                    },
                    "product_type_attribute_choices": [],
                    "title_foreign_key": "Shopify Product - Image (image)",
                    "title_alias": "Shopify Product - Image (shopify_product_image)"
                },
                {
                    "id": 4,
                    "uuid_id": "e7b80405-0587-4499-b2a0-7f24a71f029e",
                    "foreign_key": "product_type_name",
                    "title": "Shopify Product - Product Type",
                    "alias": "shopify_product_product_type",
                    "type": "string",
                    "description": "",
                    "empty_value": 0,
                    "wysiwyg": 0,
                    "created": "2024-03-23T00:17:14+00:00",
                    "created_by": 1,
                    "modified": "2024-03-23T00:17:14+00:00",
                    "modified_by": 1,
                    "deleted": null,
                    "deleted_by": null,
                    "_joinData": {
                        "id": 4,
                        "product_type_id": 3,
                        "product_type_attribute_id": 4,
                        "position": 0
                    },
                    "product_type_attribute_choices": [],
                    "title_foreign_key": "Shopify Product - Product Type (product_type_name)",
                    "title_alias": "Shopify Product - Product Type (shopify_product_product_type)"
                },
                {
                    "id": 7,
                    "uuid_id": "45d4f901-423a-430b-acc2-30ae68f9833c",
                    "foreign_key": null,
                    "title": "Shopify Product - Status",
                    "alias": "shopify_product_status",
                    "type": "select",
                    "description": "",
                    "empty_value": 0,
                    "wysiwyg": 0,
                    "created": "2024-03-23T00:17:14+00:00",
                    "created_by": 1,
                    "modified": "2024-03-23T00:17:14+00:00",
                    "modified_by": 1,
                    "deleted": null,
                    "deleted_by": null,
                    "_joinData": {
                        "id": 7,
                        "product_type_id": 3,
                        "product_type_attribute_id": 7,
                        "position": 0
                    },
                    "product_type_attribute_choices": [
                        {
                            "id": 1,
                            "product_type_attribute_id": 7,
                            "value": "Active",
                            "created": "2024-03-23T00:17:14+00:00",
                            "created_by": 1,
                            "modified": "2024-03-23T00:17:14+00:00",
                            "modified_by": 1,
                            "deleted": null,
                            "deleted_by": null
                        },
                        {
                            "id": 2,
                            "product_type_attribute_id": 7,
                            "value": "Inactive",
                            "created": "2024-03-23T00:17:14+00:00",
                            "created_by": 1,
                            "modified": "2024-03-23T00:17:14+00:00",
                            "modified_by": 1,
                            "deleted": null,
                            "deleted_by": null
                        }
                    ],
                    "title_foreign_key": "",
                    "title_alias": "Shopify Product - Status (shopify_product_status)"
                },
                {
                    "id": 1,
                    "uuid_id": "bb40a87a-2311-4eb1-b0f7-b765d63f1396",
                    "foreign_key": "product_name_with_manufacturer",
                    "title": "Shopify Product - Title",
                    "alias": "shopify_product_title",
                    "type": "text",
                    "description": "",
                    "empty_value": 0,
                    "wysiwyg": 0,
                    "created": "2024-03-23T00:17:14+00:00",
                    "created_by": 1,
                    "modified": "2024-03-23T00:17:14+00:00",
                    "modified_by": 1,
                    "deleted": null,
                    "deleted_by": null,
                    "_joinData": {
                        "id": 1,
                        "product_type_id": 3,
                        "product_type_attribute_id": 1,
                        "position": 0
                    },
                    "product_type_attribute_choices": [],
                    "title_foreign_key": "Shopify Product - Title (product_name_with_manufacturer)",
                    "title_alias": "Shopify Product - Title (shopify_product_title)"
                },
                {
                    "id": 3,
                    "uuid_id": "269c26e8-eb5e-49a5-8653-a94ad3db1045",
                    "foreign_key": "manufacturer_name",
                    "title": "Shopify Product - Vendor",
                    "alias": "shopify_product_vendor",
                    "type": "string",
                    "description": "",
                    "empty_value": 0,
                    "wysiwyg": 0,
                    "created": "2024-03-23T00:17:14+00:00",
                    "created_by": 1,
                    "modified": "2024-03-23T00:17:14+00:00",
                    "modified_by": 1,
                    "deleted": null,
                    "deleted_by": null,
                    "_joinData": {
                        "id": 3,
                        "product_type_id": 3,
                        "product_type_attribute_id": 3,
                        "position": 0
                    },
                    "product_type_attribute_choices": [],
                    "title_foreign_key": "Shopify Product - Vendor (manufacturer_name)",
                    "title_alias": "Shopify Product - Vendor (shopify_product_vendor)"
                }
            ],
            "title_foreign_key": "",
            "title_alias": "Shopify (shopify)"
        },
        "product_tax_class": {
            "id": 2,
            "uuid_id": "c2ae2975-bf4d-4cd3-b224-0ba41d46b6f3",
            "foreign_key": null,
            "title": "B2B - Business to Business National",
            "alias": "b2b-business-to-business-national",
            "tax": "19.0000",
            "description": "B2B National",
            "created": "2024-03-23T00:17:14+00:00",
            "created_by": 1,
            "modified": "2024-03-23T00:17:14+00:00",
            "modified_by": 1,
            "deleted": null,
            "deleted_by": null,
            "title_foreign_key": "",
            "title_alias": "B2B - Business to Business National (b2b-business-to-business-national)"
        },
        "product_manufacturer": {
            "id": 17,
            "uuid_id": "d0a47835-43c4-4368-be45-e133910b3083",
            "foreign_key": "20000264",
            "name": "Canon",
            "slug": "canon",
            "website": "",
            "description": "OEM",
            "logo": "",
            "image": "",
            "status": 1,
            "created": "2024-03-23T00:17:14+00:00",
            "created_by": 1,
            "modified": "2024-03-23T00:17:14+00:00",
            "modified_by": 1,
            "deleted": null,
            "deleted_by": null,
            "name_foreign_key": "Canon (20000264)"
        },
        "product_delivery_time": {
            "id": 2,
            "uuid_id": "07d8bc9d-d932-4b6e-a162-d834fe7e87e0",
            "foreign_key": null,
            "title": "1-2 Days",
            "alias": "1-2-days",
            "description": "Logisticians may vary.",
            "created": "2024-03-23T00:17:14+00:00",
            "created_by": 1,
            "modified": "2024-03-23T00:17:14+00:00",
            "modified_by": 1,
            "deleted": null,
            "deleted_by": null,
            "title_foreign_key": "",
            "title_alias": "1-2 Days (1-2-days)"
        },
        "product_condition": {
            "id": 1,
            "uuid_id": "4ed8be6c-c046-4dab-b3dc-76f4083af633",
            "foreign_key": null,
            "title": "New",
            "alias": "new",
            "description": "New product.",
            "created": "2024-03-23T00:17:14+00:00",
            "created_by": 1,
            "modified": "2024-03-23T00:17:14+00:00",
            "modified_by": 1,
            "deleted": null,
            "deleted_by": null,
            "title_foreign_key": "",
            "title_alias": "New (new)"
        },
        "product_categories": [
            {
                "id": 184,
                "parent_id": null,
                "uuid_id": "010238d7-96fc-46cb-a737-ba65895132e1",
                "foreign_key": "145211100",
                "lft": 367,
                "rght": 368,
                "name": "Toner black",
                "slug": "toner-black",
                "description": "",
                "background_image": "",
                "meta_description": "",
                "meta_keywords": "",
                "locale": "en_US",
                "status": 1,
                "created": "2024-03-23T00:17:14+00:00",
                "created_by": 1,
                "modified": "2024-03-23T00:17:14+00:00",
                "modified_by": 1,
                "deleted": null,
                "deleted_by": null,
                "_joinData": {
                    "id": 1,
                    "product_category_id": 184,
                    "product_id": 1,
                    "position": 0
                },
                "name_foreign_key": "Toner black (145211100)",
                "name_locale": "Toner black (en_US)",
                "name_slug_locale": "Toner black (toner-black) (en_US)"
            }
        ],
        "product_brands": [],
        "shopify_product_body_html": "Canon 1557A003/FX-3 Toner cartridge black, 2.700 pages/5%",
        "shopify_product_handle": "canon-fx3-CRGFX3-EPFX3-toner-cartridge-black-1557A003-1557A003AA-1557A003BA",
        "shopify_product_image": "66957",
        "shopify_product_product_type": "Toner black",
        "shopify_product_status": "Active",
        "shopify_product_title": "Canon FX3,CRGFX3,EPFX3 Toner cartridge black - 1557A003,1557A003AA,1557A003BA",
        "shopify_product_vendor": "Canon"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product suppliers'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-suppliers</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-suppliers/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "9a054906-d245-4c82-b1f6-492ae0a0b2e6",
        "foreign_key": null,
        "number": "00000000001",
        "name": "Bechlem GmbH",
        "name_addition": null,
        "street": "Am Elfengrund 23",
        "street_addition": null,
        "postcode": "64297",
        "city": "Darmstadt",
        "country": "Deutschland",
        "status": 1,
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "name_number": "Bechlem GmbH (00000000001)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product tax classes'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-tax-classes</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-tax-classes/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "cc937ea3-7737-4ab3-aa0e-adf7b81103dc",
        "foreign_key": null,
        "title": "B2B - Business to Business International",
        "alias": "b2b-business-to-business-international",
        "tax": "0.0000",
        "description": "B2B International",
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "title_foreign_key": "",
        "title_alias": "B2B - Business to Business International (b2b-business-to-business-international)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product type attribute choices'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-type-attribute-choices</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-type-attribute-choices/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "product_type_attribute_id": 7,
        "value": "Active",
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product type attributes'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-type-attributes</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-type-attributes/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "bb40a87a-2311-4eb1-b0f7-b765d63f1396",
        "foreign_key": "product_name_with_manufacturer",
        "title": "Shopify Product - Title",
        "alias": "shopify_product_title",
        "type": "text",
        "description": "<p><br></p>",
        "empty_value": 0,
        "wysiwyg": 0,
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "title_foreign_key": "Shopify Product - Title (product_name_with_manufacturer)",
        "title_alias": "Shopify Product - Title (shopify_product_title)"
    }
}
        </code></pre></small><br />
        <br />
        <strong><?= __d('bechlem_connect_light', 'Product types'); ?></strong><br />
        <br />
        <?= __d('bechlem_connect_light', 'Request'); ?>
        <small><pre><code class="language-php">POST GET PUT DELETE <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-types</code></pre></small>
        <?= __d('bechlem_connect_light', 'Response'); ?>
        <small><pre><code class="language-php">GET <?= 'http' . h($s) . '://' . h($httpHost); ?>/api/product-types/1<br />
        <br />
{
    "success": true,
    "data": {
        "id": 1,
        "uuid_id": "a3891c93-6ed1-4718-bd47-1152d5280722",
        "foreign_key": null,
        "title": "Default",
        "alias": "default",
        "description": "<p>This is the \"Default\" BECHLEM CONNECT LIGHT product type.</p>",
        "created": "2024-03-23T00:17:14+00:00",
        "created_by": 1,
        "modified": "2024-03-23T00:17:14+00:00",
        "modified_by": 1,
        "deleted": null,
        "deleted_by": null,
        "title_foreign_key": "",
        "title_alias": "Default (default)"
    }
}
        </code></pre></small>
    </div>
</div>

<?= $this->Html->css('BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'prism' . DS . 'prism.min', ['fullBase' => true]); ?>
<?= $this->Html->script(
    'BechlemConnectLight' . '.' . 'admin' . DS . 'vendor' . DS . 'prism' . DS . 'prism.min',
    ['block' => 'scriptBottom', 'fullBase' => true]); ?>