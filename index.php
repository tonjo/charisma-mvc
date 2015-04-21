<?php

/**
 * A simple PHP MVC skeleton
 *
 * @package php-mvc
 * @author Panique
 * @link http://www.php-mvc.net
 * @link https://github.com/panique/php-mvc/
 * @license http://opensource.org/licenses/MIT MIT License
 */

// load helper functions
require 'application/libs/util.php';

if (($req = Util::performMinimumRequirementsCheck()) !== true)
    die($req);

// load the (optional) Composer auto-loader
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
}
else {
    die("<b>Errors</b>, maybe you should read README.md file. You must perform actions like <pre># composer install</pre> and run  SQL scripts in <b>application/_install</b>");
}

// load application config (error reporting etc.)
require 'application/config/config.php';

// load application class
require 'application/libs/application.php';
require 'application/libs/controller.php';

// load GenericModel class
require 'application/models/genericmodel.php';

// load UserModel class
require 'application/models/usermodel.php';

// load PHPMailer autoloader class
//require 'vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

// run the scss compiler every you the application is hit (in development)
// TODO: build a switch for development/production
// SassCompiler::run("public/scss/", "public/css/");

// start the application
$app = new Application();
