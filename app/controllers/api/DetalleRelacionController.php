<?php

class DetalleRelacionController extends BaseController {

	public function detalle($relacion){

		$detalle = DB::table('FolioEtapaEntrega')
		->select(DB::raw('RelacionFiscal.REL_clave, FEE_Folio, FEE_Etapa, FEE_entrega,TII_tipo, TRA_fecha, TRA_obs, REL_global,
			             CASE FEE_Etapa WHEN 1 THEN CONVERT(varchar, EXP_pagoET1,1) WHEN 2 THEN CONVERT(varchar, EXP_pagoET2, 1) ELSE CONVERT(varchar, EXP_pagoET3, 1) END as Reserva' ))
		->join('Tramite', 'FolioEtapaEntrega.FEE_clave', '=', 'Tramite.TRA_llave')
		->join('TramiteRelacion', 'Tramite.TRA_clave', '=','TramiteRelacion.TRA_clave')
		->join('RelacionFiscal', 'TramiteRelacion.REL_clave', '=','RelacionFiscal.REL_clave')
		->join('TramiteTipo', 'Tramite.TRA_tipo', '=', 'TramiteTipo.TII_clave')
		->join('TipoConcepto', 'Tramite.TCO_concepto', '=', 'TipoConcepto.TCO_clave')
        ->join('Pase',  'Pase.PAS_folio', '=', 'FolioEtapaEntrega.FEE_Folio')
        ->join('Etapa1',  'Etapa1.PAS_folio', '=', 'Pase.PAS_folio')
        ->join('Expediente',  'Expediente.ET1_claveint', '=', 'Etapa1.ET1_claveint')
		->where('RelacionFiscal.REL_clave', '=', $relacion)
		->get();
		
		return $detalle;
	}


	public function upload()
	{
	    // if(!\Input::file("file"))
	    // {
	    //     return redirect('upload')->with('error-message', 'File has required field');
	    // }else{
	    // 	return Response::json(array('respuesta' => 'Bien'));
	    // }
	    $fecha = date('Y-m-d');
	    $mime = \Input::file('file')->getMimeType();
	    $extension = strtolower(\Input::file('file')->getClientOriginalExtension());
	    $fileName = $fecha.'-'.uniqid().'.'.$extension;
	    $path = "Complementos/";

	    if (\Request::file('file')->isValid())
	    {
	        \Request::file('file')->move($path, $fileName);
	    }
	    $directorio = opendir($path); //ruta actual
	    while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
	    {
	        if (is_dir($archivo))//verificamos si es o no un directorio
	        {
	            //echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
	        }
	        else
	        {   
				$archivos[] = $archivo;  
	            
	        }
	    }

	    return Response::json(array('respuesta' => $archivos));

	}


}