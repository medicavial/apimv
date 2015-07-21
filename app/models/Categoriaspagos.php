<?php 

class Categoriaspagos extends Eloquent {
	
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='TCat_clave';
    protected $table = 'TicketPagosCat';

}		
