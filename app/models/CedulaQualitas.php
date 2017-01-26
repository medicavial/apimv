<?php 

class CedulaQualitas extends Eloquent {
	
	public $timestamps = false;
	protected $connection = 'mysql';
	protected $primaryKey ='CQ_id';
    protected $table = 'CartasQualitas';

}		
