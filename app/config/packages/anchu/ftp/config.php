<?php
return array(

    /*
	|--------------------------------------------------------------------------
	| Default FTP Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the FTP connections below you wish
	| to use as your default connection for all ftp work.
	|
	*/

    'default' => 'connection1',

    /*
    |--------------------------------------------------------------------------
    | FTP Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the FTP connections setup for your application.
    |
    */

    'connections' => array(

        'connection1' => array(
            'host'   => 'ftp.medicavial.net',
            'port'  => 21,
            'username' => 'medica',
            'password'   => 'm3D$k4_100i',
            'passive'   => false,
        ),


        'connection2' => array(
            'host'   => '172.17.10.9',
            'port'  => 21,
            'username' => 'admin',
            'password'   => 'Med1$4_01i0',
            'passive'   => false,
        ),
    ),
);