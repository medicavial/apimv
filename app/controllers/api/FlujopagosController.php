<?php

class FlujopagosController extends BaseController {


	//Consulta de flujo de pagos
	public function general(){
		return DB::select("EXEC MV_FLU_ListaPagos");
	}

	public function fecharecepcion(){
		$fechaini =  Input::get('fechainiRec'); 
	    $fechafin =  Input::get('fechafinRec'). ' 23:59:58.999'; 
		return DB::select("EXEC MV_FLU_ListaPagosXfechaRecepcion @fechaini = '$fechaini', @fechafin = '$fechafin'");
	}

	public function fechapagos(){
		$fechaini =  Input::get('fechainiPag'); 
	    $fechafin =  Input::get('fechafinPag'). ' 23:59:58.999'; 
		return DB::select("EXEC MV_FLU_ListaPagosXfechaRecepcionPagos @fechaini = '$fechaini', @fechafin = '$fechafin'");
	}

}
