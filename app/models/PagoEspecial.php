<?php 

class PagoEspecial extends Eloquent {
	
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='PEU_clave';
    protected $table = 'PagoEspecialUnidad';

}
