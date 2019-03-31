<?php

use App\Utils\Tools;
$dotenv = Dotenv\Dotenv::create('../');
$dotenv->load();

define('APP_NAME', !empty(getenv('APP_NAME')) ? getenv('APP_NAME') : 'Mi proyecto');

define('DDBB_NAME', !empty(getenv('DDBB_NAME')) ? getenv('DDBB_NAME') : 'test');

define('DDBB_HOST', !empty(getenv('DDBB_HOST')) ? getenv('DDBB_HOST') : 'localhost');

define('DDBB_USER', !empty(getenv('DDBB_USER')) ? getenv('DDBB_USER') : 'root');

define('DDBB_PASS', getenv('DDBB_PASS'));

define('EXPIRATION_TIME', !empty(getenv('EXPIRATION_TIME')) ? getenv('EXPIRATION_TIME') : 60);

define('PUBLIC_KEY', !empty(getenv('PUBLIC_KEY')) ? getenv('PUBLIC_KEY') : 'public_secret');

define('PRIVATE_KEY', !empty(getenv('PRIVATE_KEY')) ? getenv('PRIVATE_KEY') : 'private_secret');

define('TIME_ZONE', !empty(getenv('TIME_ZONE')) ? getenv('TIME_ZONE') : 'America/Argentina/Cordoba');

define('SESSION_NAME', Tools::cleanString(APP_NAME));

define('APP_PUBLIC_URL', !empty(getenv('APP_PUBLIC_URL')) ? getenv('APP_PUBLIC_URL') : $_SERVER['DOCUMENT_ROOT']);

date_default_timezone_set(TIME_ZONE);

define('EMAIL_HOST', getenv('EMAIL_HOST'));

define('EMAIL_USER', getenv('EMAIL_USER'));

define('EMAIL_PASS', getenv('EMAIL_PASS'));

define('EMAIL_PORT', getenv('EMAIL_PORT'));

define('EMAIL_TOKEN_EXPIRATION_TIME', !empty(getenv('EMAIL_TOKEN_EXPIRATION_TIME')) ? getenv('EMAIL_TOKEN_EXPIRATION_TIME') : 1440);
