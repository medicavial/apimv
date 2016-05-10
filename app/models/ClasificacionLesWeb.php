<?php 

class ClasificacionLesWeb extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='ClasL_clave';
    protected $table = 'ClasificacionLes';
}		
