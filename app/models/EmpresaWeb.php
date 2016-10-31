<?php 

class EmpresaWeb extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='Cia_clave';
    protected $table = 'Compania';
}		
