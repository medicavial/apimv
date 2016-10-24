<?php 

class HistorialWeb extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='HIS_clave';
    protected $table = 'Historial';

}		
