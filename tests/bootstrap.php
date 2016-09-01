<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ( ! @include_once __DIR__.'/../vendor/autoload.php')
{
	exit("You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
");
}
