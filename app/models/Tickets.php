<?php 

class Tickets extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
    protected $table = 'TicketSeguimiento';
    protected $primaryKey ='TSeg_clave';
}


		
