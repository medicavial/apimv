<?php

class RelacionNoPagadaController extends BaseController {

	public function fechaRegistro(){

	    DB::disableQueryLog();
		  $fechaini =  Input::get('fechainiReg').' 00:00:00'; 
	    $fechafin =  Input::get('fechafinReg').' 23:59:59'; 

		  $relacionado =DB::table('RelacionPago')
                              ->select('RelacionPago.REL_clave as relacion','REL_subtotal as subtotal', 'REL_total as total', 'UNI_nombrecorto as unidad', 'REL_fecha as fecha')
                              ->join('OrdenPago', 'RelacionPago.REL_clave','=', 'OrdenPago.REL_clave')
                              ->join('Documento', 'OrdenPago.DOC_folio','=', 'Documento.DOC_folio')
                              ->join('Unidad', 'Documento.UNI_claveint', '=', 'Unidad.UNI_claveint')
                              ->join('Relacion', 'RelacionPago.REL_clave', '=', 'Relacion.REL_clave')
                              ->whereBetween('Relacion.REL_fecha', array($fechaini, $fechafin))
                              ->distinct()
                              ->get();
      return $relacionado;

      // , 'REL_subtotal as subtotal', 'REL_total as total', 'UNI_nombrecorto as unidad', 'REL_fecha as fecha'
	}

      public function generaReporte(){

            $datos=  Input::all();   
            return Excel::create('RelacionPagos', function ($excel) use($datos){

                $excel->sheet('Sheetname', function ($sheet) use($datos){

                    $sheet->fromArray($datos);
                    $sheet->mergeCells('A1:E1');
                    $sheet->row(1, function ($row) {
                        $row->setFontFamily('Comic Sans MS');
                        $row->setFontSize(14);
                    });

                    $sheet->row(1, array('Relacion Pagos'));
                    // setting column names for data - you can of course set it manually
                    $sheet->row(2, array('Relacion', 'Subtotal', 'Total', 'Unidad','Fecha Creada'
                              ));
                    // getting last row number (the one we already filled and setting it to bold
                    $sheet->row($sheet->getHighestRow(), function ($row) {
                        $row->setFontWeight('bold');
                    });

                });
            })->store('xls', public_path('exports') , true);
            
      }

      // 'Ref','Aseguradora','N°','Factura', 'Folio Fiscal','Importe Factura','Total','Fecha Captura','Tipo Lesion','Diagnostico','ET','Pagar','Nombre Lesionado'


}
