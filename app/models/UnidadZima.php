<?php 

class UnidadZima extends Eloquent {

	public $timestamps = false;
    protected $table = 'Unidad';
    protected $connection = 'zima';
    protected $primaryKey ='UNI_clave';
}
