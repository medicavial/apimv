<?php

class QualitasController extends BaseController {

	//Actualiza y procesa todo sobre el envio los que fueron aceptados y los que no
	public function actualiza($envio){

 		$datos =  Input::all();

 		// Actualizamos el envio
		$send = Qualitas::find($envio);
		$send->ENQ_fechaprocesado = date('d/m/Y H:i:s');
		$send->ENQ_procesado = 1;
		$send->save();

		//se actualizan los folios que si fuerno validos por qualitas
 		foreach ($datos as $dato) {

 			$folio = $dato['folioSistema'];

 			DB::table('DetalleEnvio')
				->where('ENQ_claveint',$envio)
				->where('PAS_folio', $folio)
				->update(array('DEE_procesado' => 1));

 		}

 		//se toman los que no para poderlos actualizar en la tabla pase
 		$foliosNoprocesados = Qualitasdetalle::where('ENQ_claveint',$envio)
 											->where('DEE_procesado',0)->get();

 		//aqui se actualiza en pase para regresarlos
 		foreach ($foliosNoprocesados as $dato) {
 			$folio = $dato['PAS_folio'];
 			$pase = Pase::find($folio);
 			$pase->PAS_procQ = 0;
 			$pase->save();
 		}

 		$respuesta = array('respuesta' => 'Datos procesados Correctamente','faltantes' => $foliosNoprocesados);

 		return Response::json($respuesta);

	}

	public function invalidos(){
 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		return DB::select("EXEC MVQualitasWSrechazado @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");
	}

	public function envio($envio){
 		return DB::select("EXEC MVQualitasWSenviado @idenvio = $envio");
	}

	public function envios(){

		$fechaini = Input::get('fechaini');
		$fechafin = Input::get('fechafin') . ' 23:59:59';

 		$envios = DB::select("SELECT EnviosQualitas.ENQ_claveint, ENQ_fechaenvio,ENQ_procesado, (SELECT COUNT(*) from DetalleEnvio where DetalleEnvio.ENQ_claveint = EnviosQualitas.ENQ_claveint) as Cuenta FROM EnviosQualitas WHERE ENQ_fechaenvio BETWEEN '$fechaini' and '$fechafin'");

 		return $envios;
	
	}

	public function incompletos(){

 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		return DB::select("EXEC MVQualitasWS @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");
	
	}

	public function generaArchivos(){


		$datos = Input::all();

		$correctos = array();
		$incorrectos = array();
		$archivos = array();
		$correctos = array();
		$imagenes = array();
		
		$Dia = date('d');
		$Mes = date('m');
		$Anyo = date('Y');


		$clave = $this->generar_clave();
		$fechacarpeta =  $Dia . "-" . $Mes . "-" . $Anyo;
		$filename = "archivo-". $fechacarpeta . "-". $clave . ".zip";
		$carpetaexporta = storage_path().'/exports/'.$filename;

		// $zip = new ZipArchive();
		// $zip->open($filename, ZipArchive::CREATE);

		$pesototal = 14900000;//equivale a 15Mb peso total del zip
		$peso = 0;
		$pesofolio = 0;
		$maximofolios = 50;//maximo de folios por archivo
		$folio = 0;

		//verificamos folio x folio para ver si cumople con 
		foreach ($datos as $dato) {
			

			$folio = $dato['folioSistema'];
			$fecha = $dato['FechaCaptura'];

			$MesNro = date('m', strtotime($fecha));
			$DiaNro = date('d', strtotime($fecha));
			$AnyoNro = date('Y', strtotime($fecha));
			

			if($MesNro=='01'){ 
				$MesNro="1"; 
			} 

			if($MesNro=='02'){ 
				$MesNro="2"; 
			} 

			if($MesNro=='03'){ 
				$MesNro="3"; 
			} 

			if($MesNro=='04'){ 
				$MesNro="4"; 
			} 

			if($MesNro=='05'){ 
				$MesNro="5"; 
			} 

			if($MesNro=='06'){ 
				$MesNro="6"; 
			} 

			if($MesNro=='07'){ 
				$MesNro="7"; 
			} 

			if($MesNro=='08'){ 
				$MesNro="8"; 
			} 

			if($MesNro=='09'){ 
				$MesNro="9"; 
			} 

			$archivo = DB::select("EXEC MVImgs_Datos @folio = '$folio'");
			foreach ($archivo as $data) {
				$nombre = $data->Archivo;
			}

			//$ruta = "C:\\Users\\salcala.MEDICAVIAL\\Desktop\\MV\\QUALITAS\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;
			//ruta en producción
			$ruta = "\\\\Eaa\\RENAUT\\10\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;

			$archivo1 = $ruta . "\\" . $nombre . "QS07.jpg";
			$archivo2 = $ruta . "\\" . $nombre . "GN19.jpg";
			$archivo3 = $ruta . "\\" . $nombre . "ME02.pdf";
			
			if (File::exists($archivo1) && File::exists($archivo2) && File::exists($archivo3)) {


				if ($peso < $pesototal ) {

					if ($folio <= $maximofolios) {
						
						$pesoarchivo1 = filesize($archivo1);
						$pesoarchivo2 = filesize($archivo2);
						$pesoarchivo3 = filesize($archivo3);

						//sumamos el peso de los tres archivos del folio
						$pesofolio = $pesoarchivo1 + $pesoarchivo2 + $pesoarchivo3;
					    
					    $peso = $pesofolio + $peso;

					    array_push($imagenes, $archivo1);
					    array_push($imagenes, $archivo2);
					    array_push($imagenes, $archivo3);

					    array_push($archivos, $dato);

					    $folio = $folio + 1;

					}

				}  

				array_push($correctos, $dato);
			   
			}else {

				
				array_push($incorrectos, $dato);

			}

		}

		// $zip->close();
		Zipper::make($carpetaexporta)->add($imagenes);

		$resultado = array('correctos' => $correctos,'incorrectos' => $incorrectos, 'archivo' =>  $filename,'comprimidos' => $archivos);

 		return Response::json($resultado);
	
	}

	public function procesa(){

 		$correctos =  Input::get('correctos'); 
 		$incorrectos =  Input::get('incorrectos'); 
	    
	    $envio = new Qualitas;

	    $envio->ENQ_fechaenvio = date('d/m/Y H:i:s');
	    $envio->ENQ_procesado = 0;
	    $envio->save();

	    //toma el ultimo id insertado
	    $ultimo = $envio->ENQ_claveint;

	    foreach ($correctos as $dato) {
				
				$folio = $dato['folioSistema'];

				$detalle = new Qualitasdetalle;

				$detalle->ENQ_claveint = $ultimo;
				$detalle->PAS_folio = $folio;
				$detalle->DEE_procesado = 0;
				$detalle->save(); 

				$folio = Pase::find($folio);
				$folio->PAS_procQ = 1;
				$folio->save();

		}


		foreach ($incorrectos as $data) {
				
				$folio = $data['folioSistema'];

				$pase = Pase::find($folio);
				$pase->PAS_procQ = 3;
				$pase->save();

		}


		return Response::json(array('respuesta' => 'Proceso Exitoso'));
	
	}

	public function rechazos(){

 		$datos =  Input::all();
		
 		//aqui se actualiza en pase para mandarlos a rechazos
 		foreach ($datos as $dato) {
 			$folio = $dato['PAS_folio'];
 			$motivo = $dato['Motivo'];
 			$pase = Pase::find($folio);
 			$pase->PAS_procQ = 4;
 			$pase->PAS_procQObs = $motivo;
 			$pase->save();
 		}

 		return Response::json(array('respuesta' => 'Datos procesados Correctamente'));

	}

	public function sinarchivo(){

 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		return DB::select("EXEC MVQualitasWSarchivos @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");
	
	}

	public function sinprocesar(){

 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		return DB::select("EXEC MVQualitasWS @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");
	
	}

	//funciones privadas
	private function generar_clave(){ 

       	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$cad = "";
		for($i=0;$i<12;$i++) {
			$cad .= substr($str,rand(0,62),1);
		}
		return $cad;

	}

}
