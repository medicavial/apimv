<?php

class PagosController extends BaseController{

	public function globales(){

		$folios =  Input::all(); 

	// print_r($folios);
	//     foreach ($folios as $foliodato) {
	//     	$clave = $foliodato['Cliente'];
	// 	}
	return Response::json(array('respuesta' => 'Sube la Factura de esta Relación'));

  }


}
