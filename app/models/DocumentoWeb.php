<?php 

class DocumentoWeb extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='DOC_clave';
    protected $table = 'Documento';
}		
