<?php

class QualitasController extends BaseController {

	//Consulta de flujo de pagos
	public function sinprocesar(){

 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		return DB::select("EXEC MVQualitasWS @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");
	
	}

	public function generaarchivos(){
		// $correctos = array();
		// $incorrectos = array();
		// $archivos = array();
		// $correctos = array();
		
		// $Dia = date('d');
		// $Mes = date('m');
		// $Anyo = date('Y');


		// $clave = generar_clave();
		// $fechacarpeta =  $Dia . "-" . $Mes . "-" . $Anyo;
		// $filename = "facturas/archivo-". $fechacarpeta . "-". $clave . ".zip";
		// $carpetaexporta = "api/facturas/archivo-". $fechacarpeta . "-". $clave . ".zip";

		// $zip = new ZipArchive();
		// $zip->open($filename, ZipArchive::CREATE);

		// $pesototal = 14900000;//equivale a 15Mb peso total del zip
		// $peso = 0;
		// $pesofolio = 0;
		// $maximofolios = 50;//maximo de folios por archivo
		// $folio = 0;

		// //verificamos folio x folio para ver si cumople con 
		// foreach ($datos as $dato) {
			

		// 	$folio = $dato->folioSistema;
		// 	$fecha = $dato->FechaCaptura;

		// 	$MesNro = date('m', strtotime($fecha));
		// 	$DiaNro = date('d', strtotime($fecha));
		// 	$AnyoNro = date('Y', strtotime($fecha));
			

		// 	if($MesNro=='01'){ 
		// 		$MesNro="1"; 
		// 	} 

		// 	if($MesNro=='02'){ 
		// 		$MesNro="2"; 
		// 	} 

		// 	if($MesNro=='03'){ 
		// 		$MesNro="3"; 
		// 	} 

		// 	if($MesNro=='04'){ 
		// 		$MesNro="4"; 
		// 	} 

		// 	if($MesNro=='05'){ 
		// 		$MesNro="5"; 
		// 	} 

		// 	if($MesNro=='06'){ 
		// 		$MesNro="6"; 
		// 	} 

		// 	if($MesNro=='07'){ 
		// 		$MesNro="7"; 
		// 	} 

		// 	if($MesNro=='08'){ 
		// 		$MesNro="8"; 
		// 	} 

		// 	if($MesNro=='09'){ 
		// 		$MesNro="9"; 
		// 	} 

		// 	$archivo = DB::select("EXEC MVImgs_Datos @folio = '$folio'")->fisrs();
		// 	$sql = "EXEC MVImgs_Datos @folio = '$folio'";
		// 	$rs= odbc_exec($conexion,$sql);

		// 	$nombre = odbc_result($rs,"Archivo");

		// 	//$ruta = "C:\\Users\\salcala.MEDICAVIAL\\Desktop\\MV\\QUALITAS\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;
		// 	//ruta en producción
		// 	$ruta = "\\\\Eaa\\RENAUT\\10\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;

		// 	$archivo1 = $ruta . "\\" . $nombre . "QS07.jpg";
		// 	$archivo2 = $ruta . "\\" . $nombre . "GN19.jpg";
		// 	$archivo3 = $ruta . "\\" . $nombre . "ME02.pdf";
			
		// 	if (file_exists($archivo1) && file_exists($archivo2) && file_exists($archivo3)) {


		// 		if ($peso < $pesototal ) {

		// 			if ($folio <= $maximofolios) {
						
		// 				$pesoarchivo1 = filesize($archivo1);
		// 				$pesoarchivo2 = filesize($archivo2);
		// 				$pesoarchivo3 = filesize($archivo3);

		// 				//sumamos el peso de los tres archivos del folio
		// 				$pesofolio = $pesoarchivo1 + $pesoarchivo2 + $pesoarchivo3;
					    
		// 			    $peso = $pesofolio + $peso;
						
		// 				$rs= odbc_exec($conexion,$sql);

		// 			    $zip->addFile($archivo1,$nombre . "QS07.jpg");
		// 			    $zip->addFile($archivo2,$nombre . "GN19.jpg");
		// 			    $zip->addFile($archivo3,$nombre . "ME02.pdf");

		// 			    array_push($archivos, $dato);

		// 			    $folio = $folio + 1;

		// 			}

		// 		}  

		// 		array_push($correctos, $dato);
			   
		// 	}else {

				
		// 		array_push($incorrectos, $dato);

		// 	}

 	// 	return "Hola mundo";
	
	}


	private function generar_clave(){ 

       	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$cad = "";
		for($i=0;$i<12;$i++) {
		$cad .= substr($str,rand(0,62),1);
		}
		return $cad;

	}

}
