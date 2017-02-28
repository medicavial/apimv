<?php
include(app_path() . '/classes/PHPExcel.php');


class DetalleRelacionController extends BaseController {

	public function listadetalleRelacion($relacion){

		$detalle = DB::table('OrdenPago')
					->join('Relacion', 'Relacion.REL_clave', '=', 'OrdenPago.REL_clave')
					// ->join('RelacionPago', 'Relacion.REL_clave', '=', 'RelacionPago.REL_clave')
					->join('Documento',  'OrdenPago.DOC_folio', '=', 'Documento.DOC_folio')
					->join('Pase',  'OrdenPago.DOC_folio', '=', 'Pase.PAS_folio')
					->join('Etapa1', 'Pase.PAS_folio', '=', 'Etapa1.PAS_folio')
					->join('Reporte', 'Etapa1.REP_claveint', '=', 'Reporte.REP_claveint')
					->join('Triage', 'Pase.TRI_claveint', '=', 'Triage.TRI_claveint')
					->where('Relacion.REL_clave', '=', $relacion)
					->select(DB::raw('ROW_NUMBER() OVER(ORDER BY ORP_clave DESC) as Ref,
						     OrdenPago.DOC_folio as Aseguradora,
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
	                         TRI_nombre as Triage'))
		            ->distinct()	
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
	                    OrdenPago.DOC_folio as Aseguradora,
	                    ORP_foliofiscal as FolioFiscal, 
	                    ORP_factura as Factura,
	                    ORP_serie as Serie,
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
	                    UNI_nombre as Unidad,
	                    TRI_nombre as Triage

	        FROM OrdenPago
	        inner join Relacion ON Relacion.REL_clave = OrdenPago.REL_clave
	        -- inner join RelacionPago ON Relacion.REL_clave = RelacionPago.REL_clave
	        inner join Documento ON  OrdenPago.DOC_folio = Documento.DOC_folio
	        inner join Pase ON  OrdenPago.DOC_folio = Pase.PAS_folio 
	        inner join Etapa1 ON Pase.PAS_folio = Etapa1.PAS_folio
	        inner join Reporte ON Etapa1.REP_claveint = Reporte.REP_claveint
	        inner join Unidad ON Documento.UNI_claveint = Unidad.UNI_claveint
	        inner join Triage ON Pase.TRI_claveint = Triage.TRI_claveint
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

$style = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 13,
        'name'  => 'Verdana'
),
    'alignment' => array(
    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
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
$i = 7;

$sumaSubtotal = 0; 
$sumaIVA = 0;
$sumaTotal = 0;  

$sumaSubtotalP = 0; 
$sumaIVAP = 0;
$sumaTotalP = 0;   

foreach ($archivos as $archivo){

$objPHPExcel->getActiveSheet()->mergeCells('O1:Q1');
$objPHPExcel->getActiveSheet()->setCellValue('O1','Fecha Elaboro '. $fecha)->getStyle("O1:Q1")->applyFromArray($style_titulos);

$objPHPExcel->getActiveSheet()->mergeCells('O2:Q2');
$objPHPExcel->getActiveSheet()->setCellValue('O2',"RELAUT")->getStyle("O2:Q2")->applyFromArray($style_header);

$objPHPExcel->getActiveSheet()->mergeCells('A3:N3');
$objPHPExcel->getActiveSheet()->setCellValue('A3','RELACION DE PAGOS')->getStyle("A3")->applyFromArray($style);

$objPHPExcel->getActiveSheet()->mergeCells('A4:N4');
$objPHPExcel->getActiveSheet()->setCellValue('A4',$archivo->Unidad)->getStyle("A4")->applyFromArray($style);


$objPHPExcel->getActiveSheet()->mergeCells('O3:Q3');
$objPHPExcel->getActiveSheet()->setCellValue('O3',$relacion);
$objPHPExcel->getActiveSheet()->getStyle('O3:Q3')->applyFromArray($style_header);

$objPHPExcel->getActiveSheet()->mergeCells('O4:Q4');
$objPHPExcel->getActiveSheet()->setCellValue('O4','MEDICAVIAL')->getStyle("O4:Q4")->applyFromArray($style_header);

$rowNumber = 6; 


//////// ENCABEZADO /////////

$objPHPExcel->getActiveSheet()->setCellValue("A6", "Ref")->getStyle("A6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("B6", "Folio")->getStyle("B6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("C6", "Triage")->getStyle("C6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("D6", "Lesionado")->getStyle("D6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("E6", "Folio Fiscal")->getStyle("E6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("F6", "Factura")->getStyle("F6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("G6", "Serie")->getStyle("G6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("H6", "Subtotal")->getStyle("H6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("I6", "IVA")->getStyle("I6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("J6", "Total")->getStyle("J6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("K6", "Fecha Captura")->getStyle("K6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("L6", "Tipo Lesion")->getStyle("L6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("M6", "Diagnostico")->getStyle("M6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("N6", "Etapa")->getStyle("N6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("O6", "Entrega")->getStyle("O6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("P6", "SubtotalP")->getStyle("P6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("Q6", "IVAP")->getStyle("Q6")->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue("R6", "TotalP")->getStyle("R6")->applyFromArray($style_titulos);


/////////////// CUERPO ////////////////////
   
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $archivo->Ref)->getStyle('A'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $archivo->Aseguradora)->getStyle('B'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $archivo->Triage)->getStyle('C'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $archivo->Lesionado)->getStyle('D'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $archivo->FolioFiscal)->getStyle('E'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $archivo->Factura)->getStyle('F'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $archivo->Serie)->getStyle('G'.$i)->applyFromArray($style_body);


$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $archivo->Subtotal)->getStyle('H'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $archivo->Subtotal)->getStyle('H'.$i)->getNumberFormat()->setFormatCode('0.00');
// $objPHPExcel->getActiveSheetIndex(0)->setCellValue('F'.$i, "=SUM(F5:F$i)");


$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $archivo->IVA)->getStyle('I'.$i)->applyFromArray($style_body);

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $archivo->Total)->getStyle('J'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $archivo->Total)->getStyle('J'.$i)->getNumberFormat()->setFormatCode('0.00');

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $archivo->FechaCaptura)->getStyle('K'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $archivo->TipoLesion)->getStyle('L'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$i, $archivo->Diagnostico)->getStyle('M'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$i, $archivo->Etapa)->getStyle('N'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$i, $archivo->Entrega)->getStyle('O'.$i)->applyFromArray($style_body);

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$i, $archivo->SubtotalP)->getStyle('P'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$i, $archivo->SubtotalP)->getStyle('P'.$i)->getNumberFormat()->setFormatCode('0.00');

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$i, $archivo->IVAP)->getStyle('Q'.$i)->applyFromArray($style_body);

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.$i, $archivo->TotalP)->getStyle('R'.$i)->applyFromArray($style_body);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.$i, $archivo->TotalP)->getStyle('R'.$i)->getNumberFormat()->setFormatCode('0.00');

$sumaSubtotal+= $archivo->Subtotal;
$sumaIVA+= $archivo->IVA;
$sumaTotal+= $archivo->Total;	

$sumaSubtotalP+= $archivo->SubtotalP;
$sumaIVAP+= $archivo->IVAP;
$sumaTotalP+= $archivo->TotalP;	

  $i++;




}

$find = $i + 4;
//////// TOTAL FACTURA
$objPHPExcel->getActiveSheet()->getCell('G'.($i))->setValue('Total')->getStyle("G".($i))->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->getCell('H'.($i))->getStyle("H".($i))->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->getCell('I'.($i))->getStyle("I".($i))->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->getCell('J'.($i))->getStyle("J".($i))->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,"=SUM(H7:H".($i-1).")")->getStyle('H'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,"=SUM(I7:I".($i-1).")")->getStyle('I'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,"=SUM(J7:J".($i-1).")")->getStyle('J'.$i)->getNumberFormat()->setFormatCode('0.00');

/////// TOTAL X PAGAR

$objPHPExcel->getActiveSheet()->getCell('O'.($i))->setValue('Total')->getStyle("O".($i))->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->getCell('P'.($i))->getStyle("P".($i))->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->getCell('Q'.($i))->getStyle("Q".($i))->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->getCell('R'.($i))->getStyle("R".($i))->applyFromArray($style_titulos);
$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,"=SUM(P7:P".($i-1).")")->getStyle('P'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,"=SUM(Q7:Q".($i-1).")")->getStyle('Q'.$i)->getNumberFormat()->setFormatCode('0.00');
$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,"=SUM(R7:R".($i-1).")")->getStyle('R'.$i)->getNumberFormat()->setFormatCode('0.00');



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