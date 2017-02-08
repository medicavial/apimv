<?php

class DetalleRelacionController extends BaseController {

	public function listadetalleRelacion($relacion){

		$detalle = DB::table('OrdenPago')
					->join('Relacion', 'Relacion.REL_clave', '=', 'OrdenPago.REL_clave')
					->join('RelacionPago', 'Relacion.REL_clave', '=', 'RelacionPago.REL_clave')
					->join('Documento',  'OrdenPago.DOC_folio', '=', 'Documento.DOC_folio')
					->join('Pase',  'OrdenPago.DOC_folio', '=', 'Pase.PAS_folio')
					->join('Etapa1', 'Pase.PAS_folio', '=', 'Etapa1.PAS_folio')
					->join('Reporte', 'Etapa1.REP_claveint', '=', 'Reporte.REP_claveint')
					->where('Relacion.REL_clave', '=', $relacion)
					->select(DB::raw('ROW_NUMBER() OVER(ORDER BY ORP_clave DESC) as Ref,
						      substring(OrdenPago.DOC_folio,0,5) as Aseguradora,
                             ORP_foliofiscal as FolioFiscal, 
                             ORP_factura as Factura,
                             ORP_importe as Importe, 
                             PAS_fechaCaptura as FechaCaptura,
	                         substring(LES_primaria,0,2) as TipoLesion, 
	                         LES_primaria as Diagnostico, 
	                         DOC_etapa as Etapa,
	                         DOC_numeroEntrega Entrega,
	                         REL_total as Total, 
	                         DOC_lesionado as Lesionado'))
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

	public function generaReporte(){

            $datos=  Input::all();   
                     
            return Excel::create('DetalleRelacion', function ($excel) use($datos){

                $excel->sheet('Detalle', function ($sheet) use($datos){

                	$sheet->fromArray($datos);
                    // setting column names for data - you can of course set it manually
                    $sheet->row(1, array('Ref','Aseguradora','FolioFiscal','Factura', 'Importe','Total','FechaCaptura','TipoLesion','Diagnostico','Etapa','Total','Lesionado'));
                    // getting last row number (the one we already filled and setting it to bold
                    // $sheet->row($sheet->getHighestRow(), function ($row){
                    //     $row->setFontWeight('bold');
                    // });
                    
                    // $sheet->mergeCells('A3:M3');
                    // $sheet->row(3, function ($row){
                    //     $row->setFontFamily('Comic Sans MS');
                    //     $row->setFontSize(14);
                    // });

                });
            })->store('xls', public_path('exports') , true);
            
      }


}