<?php 

class Atencion extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';	
	protected $primaryKey = 'ATN_clave';	
    protected $table = 'Atenciones';

}		
