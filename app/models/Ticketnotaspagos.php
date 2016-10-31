<?php 

class Ticketnotaspagos extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
    protected $table = 'TicketPagosNotas';
    protected $primaryKey ='TSeg_clave';
}


		
