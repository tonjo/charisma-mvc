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
ini_set("display_errors", 1);

/**
 * Configuration for: Project URL
 * Put your URL here, for local development "127.0.0.1" or "localhost" (plus sub-folder) is fine
 */
define('URL', 'http://127.0.0.1/charisma-mvc/');

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
// define('DB_NAME', 'charisma-mvc');
// define('DB_USER', 'root');
// define('DB_PASS', 'mysql');

/**
 * Ranks. The lower the rank, the higher the power.
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

/**
 * Configuration for: Views
 *
 * PATH_VIEWS is the path where your view files are. Don't forget the trailing slash!
 * PATH_VIEW_FILE_TYPE is the ending of your view files, like .php, .twig or similar.
 */
define('PATH_VIEWS', 'application/views/');
define('PATH_VIEW_FILE_TYPE', '.twig');

