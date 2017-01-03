<?php

class DocumentoDigital extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
    protected $table = 'DocumentosDigitales';
    protected $primaryKey ='Arc_cons';
}
