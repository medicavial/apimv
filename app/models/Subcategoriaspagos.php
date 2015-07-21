<?php 

class Subcategoriaspagos extends Eloquent {
	
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='TSub_clave';
    protected $table = 'TicketPagosSubcat';
    
}		
