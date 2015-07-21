<?php 

class Categorias extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='TCat_clave';
    protected $table = 'TicketCat';

}		
