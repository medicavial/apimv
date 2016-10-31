<?php 

class PagoUnidad extends Eloquent {
	
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='PAU_clave';
    protected $table = 'PagoUnidad';

}
