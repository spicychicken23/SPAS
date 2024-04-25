<?php

if (!defined('APP_NAME'))           define('APP_NAME' ,'SPAS');
if (!defined('APP_ORGANIZATION'))   define('APP_ORGANIZATION' ,'4MG');
if (!defined('APP_OWNER'))          define('APP_OWNER' ,'4 Man Group');
if (!defined('APP_DESCRIPTION'))    define('APP_DESCRIPTION' ,'SMK Sungai Pusu Attendance System');

if (!defined('ALLOWED_INACTIVITY_TIME'))        define('ALLOWED_INACTIVITY_TIME', time()+15*60);

if (!defined('DB_DATABASE'))        define('DB_DATABASE', 'spas_db');
if (!defined('DB_HOST'))            define('DB_HOST','localhost');
if (!defined('DB_USERNAME'))        define('DB_USERNAME','admin');
if (!defined('DB_PASSWORD'))        define('DB_PASSWORD' ,'admin');
if (!defined('DB_PORT'))            define('DB_PORT' ,'');


if (!defined('MAIL_HOST'))          define('MAIL_HOST', '5MG');
if (!defined('MAIL_USERNAME'))      define('MAIL_USERNAME', 'spas.5mg.g6@gmail.com');
if (!defined('MAIL_PASSWORD'))      define('MAIL_PASSWORD', '45t3892v7039n4578tv0');
if (!defined('MAIL_ENCRYPTION'))    define('MAIL_ENCRYPTION', 'ssl');
if (!defined('MAIL_PORT'))          define('MAIL_PORT', 465);
