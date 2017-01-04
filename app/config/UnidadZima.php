<?php 

class UnidadZima extends Eloquent {

	public $timestamps = false;
	protected $connection = 'zima';
    protected $table = 'Unidad';
    protected $primaryKey ='UNI_clave';
}
