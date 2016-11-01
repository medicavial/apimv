<?php 

class FacturaExpedienteWeb extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
    protected $table = 'FacturaExpediente';
}		
