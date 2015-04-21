<?php

/**
 * Configuration
 *
 * For more info about constants please @see http://php.net/manual/en/function.define.php
 * If you want to know why we use "define" instead of "const" @see http://stackoverflow.com/q/2447791/1114320
 */

/**
 * Configuration for: Error reporting
 * Useful to show every little problem during development, but only show hard errors in production
 */
error_reporting(E_ALL);

/**
 * Configuration for: Locale
 */

$locale = 'it_IT.UTF-8';
putenv("LC_ALL=".$locale);
setlocale(LC_ALL, $locale);
$messages="messages";
bindtextdomain($messages, "./locale");
// bind_textdomain_codeset($messages, 'UTF-8');
textdomain($messages);

/**
 * Configuration for: Project URL
 * Put your URL here, for local development "127.0.0.1" or "localhost" (plus sub-folder) is fine
* Leave the $_SERVER line if you want an "adaptive" URL scheme
 */
// define('URL', 'http://127.0.0.1/ch1/');
define('URL', 'http://'.$_SERVER['HTTP_HOST'].'/charisma-mvc/');

/**
 * Configuration for: Database
 * This is the place where you define your database credentials, database type etc.
 */

// SQLite example: no DB_HOST, DB_USER and DB_PASS, just DB_TYPE and DB_NAME
define('DB_TYPE', 'sqlite');
define('DB_NAME', 'charisma-mvc.db');

// MySQL example
// define('DB_TYPE', 'mysql');
// define('DB_HOST', '127.0.0.1');
// define('DB_NAME', 'ch1');
// define('DB_USER', 'root');
// define('DB_PASS', 'mysql');

/**
 * Configuration for: Views
 *
 * PATH_VIEWS is the path where your view files are. Don't forget the trailing slash!
 * PATH_VIEW_FILE_TYPE is the ending of your view files, like .php, .twig or similar.
 */
define('PATH_VIEWS', 'application/views/');
define('PATH_VIEW_FILE_TYPE', '.twig');

/**
 * Configuration for: Ranks
 * The lower the rank, the higher the power.
 * Imagine having three ranks:
 * define('ADMIN_RANK',0);
 * define('MANAGER_RANK',1);
 * define('EDITOR_RANK',2);
 * define('USER_RANK',3);
 * Checking if a user is at least Editor would be:
 * if ($rank <= EDITOR_RANK) ...
 */

define('ADMIN_RANK',0);
define('USER_RANK',1);
define('DEFAULT_RANK',USER_RANK);

// Enable access log in `log` table.
define('ACCESS_LOG_ENABLED',true);

// Mail configuration
// define('SMTP_HOST','localhost');
// define('SMTP_PORT',25);
// define('SMTP_AUTH',false);              // Enable SMTP authentication
// define('SMTP_USERNAME','');
// define('SMTP_PASSWORD','');
// define('SMTP_SECURE','');               // `tls` or `ssl`
// define('SMTP_FROM','ch1@localhost');
// define('SMTP_FROM_NAME','Charisma MVC');

/*** ACCESS LIMITATION ***/
// UNCOMMENT WHAT NEEDED
// FIRST define ranges or subnets for every user type
// Valid syntax: Usual CIDR like 192.168.0.0/24 or range in the form 192.168.1.50-100
// NOTE: If you want to specify a single IP, USE /32 AS SUBNET !!!

// $admin_rules = [ '127.0.0.0/8', '192.168.1.0/24' ];
// $local_user_rules = ['192.168.100.50-100', '172.16.155.10/32'];

// Now associate restrictions with users (REG EXP)
// $access_rules = [
//     '/^admin$/' => $admin_rules,
//     '/^user(.)*/' => $local_user_rules
// ];

// // Finally, set ACCESS_RULES as json encoded string
// define('ACCESS_RULES',json_encode($access_rules));
