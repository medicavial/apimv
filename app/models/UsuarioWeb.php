<?php 

class UsuarioWeb extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='Usu_login';
    protected $table = 'Usuario';
}		
