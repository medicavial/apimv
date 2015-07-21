<?php 

class Status extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='TStatus_clave';
    protected $table = 'TicketStatus';
}		
