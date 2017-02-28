<?php 

class PUFacturaRef extends Eloquent {

	public $timestamps = false;
    protected $table = 'PUFacturaRef';
    protected $connection = 'zima';
    protected $primaryKey ='idFactura';
}
