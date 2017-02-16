<?php
include(app_path() . '/classes/PHPExcel.php');


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
                             ORP_importe as Subtotal,
                             ORP_iva as IVA,
                             ORP_total as Total, 
                             PAS_fechaCaptura as FechaCaptura,
	                         substring(LES_primaria,0,2) as TipoLesion, 
	                         LES_primaria as Diagnostico, 
	                         DOC_etapa as Etapa,
	                         DOC_numeroEntrega Entrega,
	                         ORP_importe as SubtotalP,
                             ORP_iva as IVAP,
                             ORP_total as TotalP,  
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

                	print_r($datos);

                	$contadorSub = count($datos)+7;
                	$contadorIVA = count($datos)+8;
                	$contadorTotal = count($datos)+9;

                	$sheet->fromArray($datos);
                    $sheet->row(1, array('Ref','Aseguradora','FolioFiscal','Factura', 'Subtotal','IVA','Total','FechaCaptura','TipoLesion','Diagnostico','Etapa','Entrega','SubtotalP','IVAP','TotalP','Lesionado'));
                    
				    $sheet->cell('C'.$contadorSub, function($cell) {
				            $cell->setValue('Subtotal');
				    });
				    $sheet->cell('D'.$contadorSub, function($cell) {
				            $cell->setValue('Subtotal');
				    });

				    $sheet->cell('C'.$contadorIVA, function($cell) {
				            $cell->setValue('IVA');
				    });
				    $sheet->cell('C'.$contadorTotal, function($cell) {
				            $cell->setValue('Total');
				    });

            //         $sheet->mergeCells("A".($contadorSub+1).":C".($contadorSub+1), function($row){

            // 	        $row->fromArray(array(
				        //     array('data1')
				        // ));

            //         });
            //         $sheet->setCellValue('C'.$contadorIVA,'IVA');
            //         $sheet->setCellValue('C'.$contadorTotal,'Total');

                    // $sheet->mergeCells('A3:M3');
                    // $sheet->row(3, function ($row){
                    //     $row->setFontFamily('Comic Sans MS');
                    //     $row->setFontSize(14);
                    // });
                    // 
                });
            })->store('xls', public_path('exports') , true);
            
    }

public function generaReporte2($relacion){

    $sql = "SELECT  ROW_NUMBER() OVER(ORDER BY ORP_clave DESC) as Ref,
	                    substring(OrdenPago.DOC_folio,0,5) as Aseguradora,
	                    ORP_foliofiscal as FolioFiscal, 
	                    ORP_factura as Factura,
	                    ORP_importe as Subtotal,
	                    ORP_iva as IVA,
	                    ORP_total as Total, 
	                    PAS_fechaCaptura as FechaCaptura,
	                    substring(LES_primaria,0,2) as TipoLesion, 
	                    LES_primaria as Diagnostico, 
	                    DOC_etapa as Etapa,
	                    DOC_numeroEntrega Entrega,
	                    ORP_importe as SubtotalP,
	                    ORP_iva as IVAP,
	                    ORP_total as TotalP,  
	                    DOC_lesionado as Lesionado,
	                    UNI_nombre as Unidad
	        FROM OrdenPago
	        inner join Relacion ON Relacion.REL_clave = OrdenPago.REL_clave
	        inner join RelacionPago ON Relacion.REL_clave = RelacionPago.REL_clave
	        inner join Documento ON  OrdenPago.DOC_folio = Documento.DOC_folio
	        inner join Pase ON  OrdenPago.DOC_folio = Pase.PAS_folio 
	        inner join Etapa1 ON Pase.PAS_folio = Etapa1.PAS_folio
	        inner join Reporte ON Etapa1.REP_claveint = Reporte.REP_claveint
	        inner join Unidad ON Documento.UNI_claveint = Unidad.UNI_claveint
	        WHERE Relacion.REL_clave = '$relacion'";

    $archivos =  DB::connection('sqlsrv')->select($sql);

    // print_r($archivos);


$fecha = date('Y-m-d');
// $fecha = dater($fe);

$objPHPExcel = new PHPExcel();

$objPHPExcel->
	    getProperties()
		->setCreator("TEDnologia.com")
		->setLastModifiedBy("TEDnologia.com")
		->setTitle("Exportar Excel con PHP")
		->setSubject("Documento de prueba")
		->setDescription("Documento generado con PHPExcel")
		->setKeywords("usuarios phpexcel")
		->setCategory("reportes");

$style_header = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 13,
        'name'  => 'Verdana'
),
    'alignment' => array(
    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
),

	'borders' => array(
	    'allborders' => array(
	      'style' => PHPExcel_Style_Border::BORDER_THIN
	    )
	  )

);

$style_header2 = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 13,
        'name'  => 'Verdana'
),
    'alignment' => array(
    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,

));

$style_titulos = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 10,
        'name'  => 'Verdana'
    ),
    'alignment' => array(
    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,

    ),

    'borders' => array(
	    'allborders' => array(
	      'style' => PHPExcel_Style_Border::BORDER_THIN
	    )
	)

);

$style_body = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 10,
        'name'  => 'Verdana'
    ),
    'alignment' => array(
    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,

    ),

    'borders' => array(
	    'allborders' => array(
	      'style' => PHPExcel_Style_Border::BORDER_THIN
	    )
	)

);

$bordes = array(

    'borders' => array(
	    'allborders' => array(
	      'style' => PHPExcel_Style_Border::BORDER_THIN
	    )
	)

);

//  $styleArray = array(
//       'borders' => array(
//           'allborders' => array(
//               'style' => PHPExcel_Style_Border::BORDER_THIN
//           )
//       )
//   );

// $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
$i = 5;

$sumaSubtotal = 0; 
$sumaIVA = 0;
$sumaTotal = 0;  

$sumaSubtotalP = 0; 
$sumaIVAP = 0;
$sumaTotalP = 0;   

foreach ($archivos as $archivo){

$objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
$objPHPExcel->getActiveSheet()->setCellValue('A1','RELACION DE PAGOS')->getStyle("A1")->applyFromArray($style_header);

$objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
$objPHPExcel->getActiveSheet()->setCellValue('A2',$archivo->Unidad)->getStyle("A2")->applyFromArray($style_header);


$objPHPExcel->getActiveSheet()->mergeCells('O2:P2');
$objPHPExcel->getActiveSheet()->setCellValue('O2',$relacion);
$objPHPExcel->getActiveSheet()->getStyle('O2:P2')->applyFromArray($style_header);

$objPHPExcel->getActiveSheet()->mergeCells('O1:P1');
$objPHPExcel->getActiveSheet()->setCellValue('O1','MEDICA VIAL')->getStyle("O1")->applyFromArray($style_header);

$rowNumber = 5; 

$objPHPExcel->getActiveSheet()->setCellValue("A4", "Ref")->getStyle("A4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("B4", "Aseguradora")->getStyle("B4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("C4", "Folio Fiscal")->getStyle("C4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("D4", "Factura")->getStyle("D4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("E4", "Subtotal")->getStyle("E4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("F4", "IVA")->getStyle("F4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("G4", "Total")->getStyle("G4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("H4", "Fecha Captura")->getStyle("H4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("I4", "Tipo Lesion")->getStyle("I4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("J4", "Diagnostico")->getStyle("J4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("K4", "Etapa")->getStyle("K4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("L4", "Entrega")->getStyle("L4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("M4", "SubtotalP")->getStyle("M4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("N4", "IVAP")->getStyle("N4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("O4", "TotalP")->getStyle("O4")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("P4", "Lesionado")->getStyle("P4")->applyFromArray($style_titulos);

   
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $archivo->Ref)->getStyle('A'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $archivo->Aseguradora)->getStyle('B'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $archivo->FolioFiscal)->getStyle('C'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $archivo->Factura)->getStyle('D'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $archivo->Subtotal)->getStyle('E'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $archivo->Subtotal)->getStyle('E'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $archivo->IVA)->getStyle('F'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $archivo->Total)->getStyle('G'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $archivo->Subtotal)->getStyle('G'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $archivo->FechaCaptura)->getStyle('H'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $archivo->TipoLesion)->getStyle('I'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $archivo->Diagnostico)->getStyle('J'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $archivo->Etapa)->getStyle('K'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $archivo->Entrega)->getStyle('L'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$i, $archivo->SubtotalP)->getStyle('M'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$i, $archivo->SubtotalP)->getStyle('M'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$i, $archivo->IVAP)->getStyle('N'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$i, $archivo->TotalP)->getStyle('O'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$i, $archivo->TotalP)->getStyle('O'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$i, $archivo->Lesionado)->getStyle('P'.$i)->applyFromArray($style_body);

  $i++;

$sumaSubtotal+= $archivo->Subtotal;
$sumaIVA+= $archivo->IVA;
$sumaTotal+= $archivo->Total;	

$sumaSubtotalP+= $archivo->SubtotalP;
$sumaIVAP+= $archivo->IVAP;
$sumaTotalP+= $archivo->TotalP;	


}

$find = $i + 4;
//////// TOTAL FACTURA

$objPHPExcel->setActiveSheetIndex(0)->mergeCells(("A".($find+1).":D".($find+1)))->getStyle(("A".($find+1).":D".($find+1)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('A'.($find+1))->setValue('Subtotal');
$objPHPExcel->getActiveSheet()->setCellValue('E'.($find+1),$sumaSubtotal)->getStyle('E'.($find+1))->getNumberFormat()->setFormatCode('0.00');


$objPHPExcel->setActiveSheetIndex(0)->mergeCells(("A".($find+2).":D".($find+2)))->getStyle(("A".($find+2).":D".($find+2)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('A'.($find+2)) ->setValue('IVA')->getStyle('D'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->getActiveSheet()->setCellValue('E'.($find+2),$sumaIVA)->getStyle('E'.($find+2))->getNumberFormat()->setFormatCode('0.00');



$objPHPExcel->setActiveSheetIndex(0)->mergeCells(("A".($find+3).":D".($find+3)))->getStyle(("A".($find+3).":D".($find+3)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('A'.($find+3)) ->setValue('Total')->getStyle('D'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->getActiveSheet()->setCellValue('E'.($find+3),$sumaTotal)->getStyle('E'.($find+3))->getNumberFormat()->setFormatCode('0.00');

///////////////// TOTAL A PAGAR

$objPHPExcel->setActiveSheetIndex(0)->mergeCells(("F".($find+1).":L".($find+1)))->getStyle(("F".($find+1).":L".($find+1)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('F'.($find+1)) ->setValue('Subtotal')->getStyle('L'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->getActiveSheet()->setCellValue('M'.($find+1),$sumaSubtotalP)->getStyle('M'.($find+1))->getNumberFormat()->setFormatCode('0.00');


$objPHPExcel->setActiveSheetIndex(0)->mergeCells(("F".($find+2).":L".($find+2)))->getStyle(("F".($find+2).":L".($find+2)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('F'.($find+2)) ->setValue('IVA')->getStyle('L'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->getActiveSheet()->setCellValue('M'.($find+2),$sumaIVAP)->getStyle('M'.($find+2))->getNumberFormat()->setFormatCode('0.00');

$objPHPExcel->setActiveSheetIndex(0)->mergeCells(("F".($find+3).":L".($find+3)))->getStyle(("F".($find+3).":L".($find+3)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('F'.($find+3)) ->setValue('Total')->getStyle('L'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->getActiveSheet()->setCellValue('M'.($find+3),$sumaTotalP)->getStyle('M'.($find+3))->getNumberFormat()->setFormatCode('0.00');


$objPHPExcel->setActiveSheetIndex(0)->mergeCells(("A".($find+6).":H".($find+6)))->getStyle(("A".($find+6).":H".($find+6)))->applyFromArray($bordes);
$objPHPExcel->getActiveSheet()->setCellValue('A'.($find+6),"FECHA DE PROGRAMACION DE PAGO ". $fecha);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells(("A".($find+7).":B".($find+7)));
$objPHPExcel->getActiveSheet()->setCellValue('A'.($find+7),"Observaciones ");


$objPHPExcel->getActiveSheet()->getStyle('A'.($find+9).":E".($find+9))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->setCellValue('A'.($find+10),"Elaboro ");

$objPHPExcel->getActiveSheet()->getStyle('K'.($find+9).":P".($find+9))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->setCellValue('L'.($find+10),"Reviso ");

$objPHPExcel->getActiveSheet()->setTitle('Reporte');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte.xls"');
header('Cache-Control: max-age=0');


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(public_path('exports/Reporte.xls'));
// $objWriter->save('desktop'); 
exit;



}


}