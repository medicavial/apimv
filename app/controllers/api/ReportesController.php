<?php

class ReportesController extends BaseController {

	public function control(){

		$folios =  Input::all(); 

		$datos = Excel::create('Listado', function($excel) use($folios) {

				    $excel->sheet('Reporte', function($sheet) use($folios) {

				        $sheet->fromArray($folios);

				    });

				})->store('xls', false, true);

		return $datos;
	}

	public function descargar($archivo){

		$ruta = storage_path() .'/exports/'. $archivo;


	    if (File::exists($ruta))
	    {
	        return Response::download($ruta, $archivo,['Content-Length: ' . File::size($ruta)]);
	    }
	    else
	    {
	        exit('El archivo no existe en el servidor!');
	    }

	}


}
