<?php

require_once 'PHPExcel.php';
$relacion =  $_REQUEST['relacion'];

//////////////////// CONEXION /////////////////

function conectarActual(){

  $produccion = false;
 
  $db_server = 'SISTEMAS4';
  $db_name = 'MV2';

 try {

     $conn = new PDO("sqlsrv:Server=$db_server;Database=$db_name", "sa", "ACc3soMv");
     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return $conn;

 } catch (PDOException $e){

  //echo "Failed to get DB handle: " . $e->getMessage() . "\n";
  exit;

 }

}

////////////// 	FECHA CON LETRA ////////////////////////////

function dater($x) {
   $year = substr($x, 0, 4);
   $mon = substr($x, 5, 2);
   switch($mon){
      case "01":
         $month = "Enero";
         break;
      case "02":
         $month = "Febrero";
         break;
      case "03":
         $month = "Marzo";
         break;
      case "04":
         $month = "Abril";
         break;
      case "05":
         $month = "Mayo";
         break;
      case "06":
         $month = "Junio";
         break;
      case "07":
         $month = "Julio";
         break;
      case "08":
         $month = "Agosto";
         break;
      case "09":
         $month = "Septiembre";
         break;
      case "10":
         $month = "Octubre";
         break;
      case "11":
         $month = "Noviembre";
         break;
      case "12":
         $month = "Diciembre";
         break;
   }
   $day = substr($x, 8, 2);
   return $day." de ".$month." de ".$year;
}

///////////////

$conexion = conectarActual();

$sql = "SELECT ROW_NUMBER() OVER(ORDER BY ORP_clave DESC) as Ref,
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
                    DOC_lesionado as Lesionado
        FROM OrdenPago
        inner join Relacion ON Relacion.REL_clave = OrdenPago.REL_clave
        inner join RelacionPago ON Relacion.REL_clave = RelacionPago.REL_clave
        inner join Documento ON  OrdenPago.DOC_folio = Documento.DOC_folio
        inner join Pase ON  OrdenPago.DOC_folio = Pase.PAS_folio 
        inner join Etapa1 ON Pase.PAS_folio = Etapa1.PAS_folio
        inner join Reporte ON Etapa1.REP_claveint = Reporte.REP_claveint
        WHERE Relacion.REL_clave = '$relacion'";
$result = $conexion->prepare($sql);  
$result->execute();

// $fecha = 

$fe = date('Y-m-d');
$fecha = dater($fe);

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


$rowNumber = 6; 

$tituloReporte = "RELACION DE PAGOS PARA EL DIA".$fecha;

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B2',$tituloReporte)
            ->setCellValue('A5', 'Referenciado')
            ->setCellValue('B5', 'Cuenta')
            ->setCellValue('C5', 'Concepto')
            ->setCellValue('D5', 'Observaciones')
            ->setCellValue('E5', 'Importe')
            ->setCellValue('F5', 'IVA')
            ->setCellValue('G5', 'Total');

while ($row = $result->fetch(PDO::FETCH_ASSOC)){

    $col = 'A'; 

    // $objPHPExcel->getActiveSheet()->setCellValue("A1", $fecha);

    foreach($row  as $key=>$cell){

    	$objPHPExcel->getActiveSheet()->getStyle($col.$rowNumber)->applyFromArray(
		    array(
		        'fill' => array(
		            'type' => PHPExcel_Style_Fill::FILL_SOLID,
		            'color' => array('rgb' => 'ECEAEA')
		        )
		    )
		);
        $objPHPExcel->getActiveSheet()->setCellValue($col.$rowNumber, $cell); 
        $col++; 

        if ($cell == $row['TotalTransf'] || $cell == $row['Transf'] || $cell == $row['Pendiente'] || $cell == $row['TotalPendiente'] ){


    	  $objPHPExcel->getActiveSheet()->getStyle($col.$rowNumber)->getNumberFormat()->setFormatCode('0.00');
     	
         } 
    } 
    $rowNumber++; 
}

$estiloTituloReporte = array(
    'font' => array(
        'name'      => 'Verdana',
        'bold'      => true,
        'italic'    => false,
        'strike'    => false,
        'size' =>16,
        'color'     => array(
            'rgb' => 'FFFFFF'
        )
    ),
    'fill' => array(
      'type'  => PHPExcel_Style_Fill::FILL_SOLID,
      'color' => array(
            'argb' => 'FF220835')
  ),
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_NONE
        )
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'rotation' => 0,
        'wrap' => TRUE
    )
);

$style_pie = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 10,
        'name'  => 'Verdana'
));

// $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($estiloTituloReporte);
$objPHPExcel->getActiveSheet()->setTitle('Listado Relación');
$objPHPExcel->setActiveSheetIndex(0);


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="listadoRelacion.xls"');
header('Cache-Control: max-age=0');


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;


?>
