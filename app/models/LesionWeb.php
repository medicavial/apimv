<?php 

class LesionWeb extends Eloquent {

	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='LES_clave';
    protected $table = 'Lesion';

}		
