# BECHLEM-CONNECT-LIGHT

**Bechlem Connect Light** is a free, open source, middlewrae and product content management system for PHP 8 based on CakePHP 5, CakePHP vendor plugins, the AdminLTE bootstrap admin dashboard template and released under the [MIT License](https://github.com/MarksSoftwareGmbH/BECHLEM-CONNECT-LIGHT/blob/main/LICENSE).

It is powered by the [CakePHP](http://cakephp.org) 5 PHP framework.

## Requirements
  * Apache with `mod_rewrite`, intl, imagick OR nginx server
  * PHP 8.1 or higher
  * Latest MySQL or MariaDB, PostgreSQL, SQLite or SQL-Server database engines

## Installation

#### Installation using GitHub [Clone](https://docs.github.com/en/get-started/getting-started-with-git/about-remote-repositories)

The preferred way to install BECHLEM-CONNECT-LIGHT is by using HTTP

    https://github.com/MarksSoftwareGmbH/BECHLEM-CONNECT-LIGHT.git
    
or by using GitHub CLI

    gh repo clone MarksSoftwareGmbH/BECHLEM-CONNECT-LIGHT

When the cloning is finished please enter the cloned project and run

    composer update

When the composer update is finished

  * Upload the project content into a server htdocs TLD folder.
  * Create a new MySQL or MariaDB database (with the `utf8mb4_unicode_ci` collation).
  * Create a new user, set the password and grant the rights for the created database.
  * Update the file /config/app_local.php Datasources array with the database, username and password for the `default`, `test_mysql`, `development` and `production` configurations.
  * Update the file /config/app_local.php EmailTransport array with a default email server configuration (SMTP or Mail transport).
  * Visit http://your-site.com/ from your browser.
  * Visit http://your-site.com/login to login as Admin, the default username: Admin and password: superadmin (Please change this credentials to your needs).
  * Visit http://your-site.com/admin/dashboard to manage the backend as Admin.

#### Installation using the ZIP [Archive](https://github.com/MarksSoftwareGmbH/BECHLEM-CONNECT-LIGHT/archive/refs/heads/main.zip)

  * Download and extract the ZIP-file.
  * Run `composer update` 
  * Upload the unzipped archive content into a server htdocs TLD folder.
  * Create a new MySQL or MariaDB database (with the `utf8mb4_unicode_ci` collation).
  * Create a new user, set the password and grant the rights for the created database.
  * Update the file /config/app_local.php Datasources array with the database, username and password for the `default`, `test_mysql`, `development` and `production` configurations.
  * Update the file /config/app_local.php EmailTransport array with a default email server configuration (SMTP or Mail transport).
  * Visit http://your-site.com/ from your browser.
  * Visit http://your-site.com/login to login as Admin, the default username: Admin and password: superadmin (Please change this credentials to your needs).
  * Visit http://your-site.com/admin/dashboard to manage the backend as Admin.

## Bechlem GmbH API Version 1.2 CrossSellingData and Simple Tables License

  * **Registration - API License**: [Contact Form](https://www.bechlem.de/241-2/)
  * **Documentation - Demo License**: [api/v12](https://www.datawriter.de/api/v12/documentation.php)

## Vendor plugins

  * **CakePHP - Application Skeleton**: [cakephp/app](https://github.com/cakephp/app)
  * **CakePHP - Authentication**: [cakephp/authentication](https://github.com/cakephp/authentication)
  * **Mark Scherer - TinyAuth Plugin**: [dereuromark/cakephp-tinyauth](https://github.com/dereuromark/cakephp-tinyauth)
  * **PHP - JWT**: [firebase/php-jwt](https://github.com/firebase/php-jwt)
  * **Friends Of Cake - Bootstrap UI**: [friendsofcake/bootstrap-ui](https://github.com/friendsofcake/bootstrap-ui)
  * **Friends Of Cake - CsvView Plugin**: [friendsofcake/cakephp-csvview](https://github.com/friendsofcake/cakephp-csvview)
  * **Friends Of Cake - CRUD Plugin**: [friendsofcake/crud](https://github.com/friendsofcake/crud)
  * **Friends Of Cake - Search Plugin**: [friendsofcake/search](https://github.com/friendsofcake/search)
  * **Intervention - PHP Image Processing**: [intervention/image](https://github.com/intervention/image)
  * **PHPOffice - PhpSpreadsheet**: [phpoffice/phpspreadsheet](https://github.com/phpoffice/phpspreadsheet)
  * **PHPOffice - PhpWord**: [phpoffice/phpword](https://github.com/phpoffice/phpword)

## Suggested vendor plugins you should install via composer

  * **Mark Story - Asset Compress**: [markstory/asset_compress](https://github.com/markstory/asset_compress)
  * **Mark Scherer - CakePHP IdeHelper Plugin**: [dereuromark/cakephp-ide-helper](https://github.com/dereuromark/cakephp-ide-helper)
  * **PHPStan - PHP Static Analysis Tool**: [phpstan/phpstan](https://github.com/phpstan/phpstan)

## Front- and Backend template

  * **Colorlib (Aigars Silkalns) - AdminLTE Bootstrap Admin Dashboard Template** [https://github.com/ColorlibHQ/AdminLTE](https://github.com/ColorlibHQ/AdminLTE)

## Links

  * **Marks Software GmbH website**: [https://www.marks-software.de](https://www.marks-software.de)
  * **Marks Software GmbH on X / Twitter**: [https://twitter.com/SoftwareMarks](https://twitter.com/SoftwareMarks)