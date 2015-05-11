<?php 

class Historico extends Eloquent {
	public $timestamps = false;
	protected $primaryKey ='HIS_claveint';
    protected $table = 'HistorialFlujo';

}		
