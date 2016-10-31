<?php

class ControlController extends BaseController {

	public function folio(){
		$folio =  Input::get('folio'); 
		return DB::select("EXEC MV_DCU_ListadoDocumentosXFolio  @folio='$folio'");
	}

	public function lesionado(){
		$lesionado =  Input::get('lesionado'); 
		return DB::select("EXEC MV_DCU_ListadoDocumentosXLesionado  @lesionado='%$lesionado%'");
	}

	public function fechas(){

		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 

		return DB::select("MV_DCU_ListadoDocumentosXFecha  @fechaIni='$fechaini', @fechaFin='$fechafin'");
	}

}
