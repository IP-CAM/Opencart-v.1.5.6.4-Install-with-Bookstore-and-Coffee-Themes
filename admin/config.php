<?php
define('SERVER_NAME',  $_SERVER['SERVER_NAME'].'/');
define('DOCUMENT_ROOT',  $_SERVER['DOCUMENT_ROOT'].'/');
define('HTTP',  'http://');
define('HTTPS',  'https://');
// HTTP
define('HTTP_SERVER', HTTP.SERVER_NAME.'admin/');
define('HTTP_CATALOG', HTTP.SERVER_NAME);

// HTTPS
define('HTTPS_SERVER', HTTPS.SERVER_NAME.'admin/');
define('HTTPS_CATALOG', HTTPS.SERVER_NAME);

// DIR
define('DIR_APPLICATION', DOCUMENT_ROOT.'admin/');
define('DIR_SYSTEM', DOCUMENT_ROOT.'system/');
define('DIR_DATABASE', DOCUMENT_ROOT.'system/database/');
define('DIR_LANGUAGE', DOCUMENT_ROOT.'admin/language/');
define('DIR_TEMPLATE', DOCUMENT_ROOT.'admin/view/template/');
define('DIR_CONFIG', DOCUMENT_ROOT.'system/config/');
define('DIR_IMAGE', DOCUMENT_ROOT.'image/');
define('DIR_CACHE', DOCUMENT_ROOT.'system/cache/');
define('DIR_DOWNLOAD', DOCUMENT_ROOT.'download/');
define('DIR_LOGS', DOCUMENT_ROOT.'system/logs/');
define('DIR_CATALOG', DOCUMENT_ROOT.'catalog/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'opencart15');
define('DB_PREFIX', 'oc_');
?>