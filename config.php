<?php
define('SERVER_NAME',  $_SERVER['SERVER_NAME'].'/');
define('DOCUMENT_ROOT',  $_SERVER['DOCUMENT_ROOT'].'/');

// HTTP
define('HTTP_SERVER', 'http://'.SERVER_NAME);

// HTTPS
define('HTTPS_SERVER', 'http://'.SERVER_NAME);



// DIR
define('DIR_APPLICATION', DOCUMENT_ROOT.'catalog/');
define('DIR_SYSTEM', DOCUMENT_ROOT.'system/');
define('DIR_DATABASE', DOCUMENT_ROOT.'system/database/');
define('DIR_LANGUAGE', DOCUMENT_ROOT.'catalog/language/');
define('DIR_TEMPLATE', DOCUMENT_ROOT.'catalog/view/theme/');
define('DIR_CONFIG', DOCUMENT_ROOT.'system/config/');
define('DIR_IMAGE', DOCUMENT_ROOT.'image/');
define('DIR_CACHE', DOCUMENT_ROOT.'system/cache/');
define('DIR_DOWNLOAD', DOCUMENT_ROOT.'download/');
define('DIR_LOGS', DOCUMENT_ROOT.'system/logs/');


// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'opencart15');
define('DB_PREFIX', 'oc_');
?>