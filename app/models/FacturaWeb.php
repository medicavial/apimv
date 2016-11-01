<?php 

class FacturaWeb extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='FAC_clave';
    protected $table = 'Factura';
}		
