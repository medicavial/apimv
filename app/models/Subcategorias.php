<?php 

class Subcategorias extends Eloquent {
	
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='TSub_clave';
    protected $table = 'TicketSubcat';
    
}		
