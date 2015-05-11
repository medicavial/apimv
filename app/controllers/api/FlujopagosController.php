<?php

class FlujopagosController extends BaseController {


	//Consulta de flujo de pagos
	public function general(){
		return DB::select("EXEC MV_FLU_ListaPagos");
	}

	public function fecharecepcion(){
		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		return DB::select("EXEC MV_FLU_ListaPagosXfechaRecepcion @fechaini = '$fechaini', @fechafin = '$fechafin'");
	}

	public function fechapagos(){
		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		return DB::select("EXEC MV_FLU_ListaPagosXfechaRecepcionPagos @fechaini = '$fechaini', @fechafin = '$fechafin'");
	}

}
