# CHARISMA-MVC

####Combines the following project:####
  - [Charisma Template](https://github.com/usmanhalalit/charisma)
  - [php-mvc-advanced](https://github.com/panique/php-mvc-advanced)
  - [php-login-one-file](https://github.com/panique/php-login-one-file)

## Installation

1. First, install Composer ([How to install Composer on Ubuntu, Debian or Windows 7/8](http://www.dev-metal.com/install-update-composer-windows-7-ubuntu-debian-centos/)).
That's some kind of PHP standard now and there's no reason to work without Composer. If you think "I don't need/want
Composer" then you are doing something seriously wrong!

2. Copy this repo into a public accessible folder on your server.
Common techniques are a) downloading and extracting the .zip / .tgz by hand, b) cloning the repo with git (into var/www)

```
git clone https://github.com/tonjo/charisma-mvc /var/www
```

3. Install mod_rewrite, for example by following this guideline:
[How to install mod_rewrite in Ubuntu](http://www.dev-metal.com/enable-mod_rewrite-ubuntu-12-04-lts/)

4. Run the SQL statements in the *application/_install* folder.
   If you're using SQLite, you can simply
   ```
   # sqlite3 charisma-mvc.db < application/_install/02-create-users-table.sql`
   ```

5. Change the .htaccess file from
```
RewriteBase /charisma-mvc/
```
to where you put this project, relative to the web root folder (usually /var/www). So when you put this project into
the web root, like directly in /var/www, then the line should look like or can be commented out:
```
RewriteBase /
```
If you have put the project into a sub-folder, then put the name of the sub-folder here:
```
RewriteBase /sub-folder/
```
In a SQLite environment, to prevent download of the database file, define a section like this:
```
<Files "charisma-mvc.db">
Order Allow,Deny
Deny from all
</Files>
```

6. Edit the *application/config/config.php*, change this line
```php
define('URL', 'http://127.0.0.1/charisma-mvc/');
```
to where your project is. Real domain, IP or 127.0.0.1 when developing locally. Make sure you put the sub-folder
in here (when installing in a sub-folder) too, also don't forget the trailing slash !

7. Edit the *application/config/config.php*, change these lines
```php
// SQLite example: no DB_HOST, DB_USER and DB_PASS, just DB_TYPE and DB_NAME
define('DB_TYPE', 'sqlite');
define('DB_NAME', 'charisma-mvc.db');
```
or these ones:
```php
// MySQL example
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'charisma-mvc');
define('DB_USER', 'root');
define('DB_PASS', 'mysql');
```
according to your database choice and setting.

8. Run `composer install` on the command line while being in the root of your project.

## A quickstart tutorial

I recommend you the well-explained [php-mvc-advanced tutorial](https://github.com/panique/php-mvc-advanced#a-quickstart-tutorial) by panique.

## Useful information

1. SQLite does not have a rowCount() method (!). Keep that in mind in case you use SQLite.

2. Don't use the same name for class and method, as this might trigger an (unintended) `__construct` of the class.
   This is really weird behaviour, but documented here: [php.net - Constructors and Destructors](http://php.net/manual/en/language.oop5.decon.php).

## Add external libraries via Composer

To add external libraries/tools/whatever into your project in an extremely clean way, simply add a line with the
repo name and version to the composer.json! Take a look on these tutorials if you want to get into Composer:
[How to install (and update) Composer on Windows 7 or Ubuntu / Debian](http://www.dev-metal.com/install-update-composer-windows-7-ubuntu-debian-centos/)
and [Getting started with Composer](http://www.dev-metal.com/getting-started-composer/).

## License

This project is licensed under the Apache 2 License.
This means you can use and modify it for free in private or commercial projects.

