<?php 

class ExpedienteInfo extends Eloquent {

	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='EXP_folio';
    protected $table = 'ExpedienteInfo';
    protected $guarded = []; 
    
}		
