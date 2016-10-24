<?php 

class BitacoraWeb extends Eloquent {
	
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='BIT_clave';
    protected $table = 'BitacoraCambio';

}		
