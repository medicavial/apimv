<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

	'fetch' => PDO::FETCH_CLASS,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => 'sqlsrv',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => __DIR__.'/../database/production.sqlite',
			'prefix'   => '',
		),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'www.medicavial.net',
			'database'  => 'medica_registromv',
			'username'  => 'medica_webusr',
			'password'  => 'tosnav50',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),

		'zima' => array(
			'driver'   => 'mysql',
			'host'     => 'www.pmzima.net',
			'database' => 'zima_sscp_3',
			'username' => 'zima_web',
			'password' => 'W3dik@_0i12',
			'charset'  => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'   => '',
		),

		'pgsql' => array(
			'driver'   => 'pgsql',
			'host'     => 'localhost',
			'database' => 'forge',
			'username' => 'forge',
			'password' => '',
			'charset'  => 'utf8',
			'prefix'   => '',
			'schema'   => 'public',
		),

		'sqlsrv' => array(
			'driver'   => 'sqlsrv',
			'host'     => 'SISTEMAS4',
			'database' => 'MV2',
			'username' => 'sa',
			// 'password' => 'sa',
			'password' => 'ACc3soMv',
			'prefix'   => '',
		),

		'test' => array(
			'driver'   => 'sqlsrv',
			'host'     => 'GEN',
			'database' => 'MV2',
			'username' => '',
			'password' => '',
			'prefix'   => '',
		),

		'production' => array(
			'driver'   => 'sqlsrv',
			'host'     => 'GEN',
			'database' => 'MV',
			'username' => '',
			'password' => '',
			'prefix'   => '',
		),

		'mysqllocal' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'medica_registro',
			'username'  => 'root',
			'password'  => '',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),

	),

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => array(

		'cluster' => false,

		'default' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		),

	),

);
