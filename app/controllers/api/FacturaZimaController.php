<?php

ini_set('memory_limit', '-1');
include(app_path() . '/classes/Historiales.php');

class FacturaZimaController extends BaseController {


	public function unidades(){

		return UnidadZima::where('UNI_activa','=', 'S')
				->select('UNI_clave as id','UNI_nomCorto as nombre')
				->orderBy('UNI_nomCorto')
				->get();
	}



}