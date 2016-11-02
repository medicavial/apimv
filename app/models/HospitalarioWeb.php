<?php 

class HospitalarioWeb extends Eloquent {
	
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='HOS_clave';
    protected $table = 'Hospitalario';

}		
