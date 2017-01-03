<?php 

class Expediente extends Eloquent {
	
	public $timestamps = false;
    protected $connection = 'mysql';
	protected $primaryKey ='Exp_folio';
    protected $table = 'Expediente';

}	