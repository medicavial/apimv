<?php

class PagoManualController extends BaseController {


    public function SubirCFDI($archivo,$archivo,$clave,$usucarpeta){

		
	  $anio = date('Y'); 
	  $mes = date('m'); 
	  $ruta = "web/FacturasPagos/";
	  $diranio =  "web/FacturasPagos/$anio";
	  $dirmes =   "web/FacturasPagos/$anio/$mes";
	  $dirfinal = "web/FacturasPagos/$anio/$mes/$clave";

	  	$ruta = FTP::connection('connection2')->getDirListing($ruta);
		$rutaanio = FTP::connection('connection2')->getDirListing($diranio);
		$rutames = FTP::connection('connection2')->getDirListing($dirmes);
		$rutafinal = FTP::connection('connection2')->getDirListing($dirfinal);

	    // ftp_chmod($id_ftp,0644,$ruta);
		if (count($rutaanio) == 0){

			FTP::connection('connection2')->makeDir($diranio);
		}

		if (count($rutames) == 0){
			FTP::connection('connection2')->makeDir($dirmes);
		}

		if (count($rutafinal) == 0){
			FTP::connection('connection2')->makeDir($dirfinal);
		}

	    $archivo_remoto = "web/FacturasPagos/$anio/$mes/$clave/$archivo";
	    $archivo_local =  "FacturasPagos/".$usucarpeta."/$archivo";

	    FTP::connection('connection2')->uploadFile($archivo_local,$archivo_remoto, FTP_ASCII);

	    $archivosFinal = FTP::connection('connection2')->getDirListing($ruta);

		// print_r($archivosFinal);
	    FTP::disconnect('connection2');

	}


	public function upload($usuario){
 
	    if (!file_exists("FacturasPagos/$usuario")) { 
	       $carpeta = mkdir("FacturasPagos/$usuario/", 0700);
	    }

        if (is_uploaded_file($_FILES['file']['tmp_name'])) {

	        $nombre =  $_FILES['file']['name'];
	        $file = str_replace(" ","",$nombre);
      
	        copy($_FILES['file']['tmp_name'], 'FacturasPagos/'.$usuario.'/'. $file);
	        $file_name = $file;
	        $subido = true;

	        //$total_imagenes = count(glob("Facturas/{*.pdf,*.xml}",GLOB_BRACE));
	        $directorio = opendir('FacturasPagos/'.$usuario.'/'); //ruta actual
	        while ($archivo1 = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
	        {
	            if (is_dir($archivo1))//verificamos si es o no un directorio
	            {
	                //echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
	            }
	            else
	            {   
	                $ext = strtolower(\Input::file('file')->getClientOriginalExtension());   //Line 32
				    // $ext = array_shift($ext);  //Line 34
	                if ($ext == 'xml' or $ext == 'XML'){
	                    
	                    $archivo[] = $archivo1;
	                }
	                
	            }
	        }

	    foreach ($archivo as $var){

            $extension = strtolower(\Input::file('file')->getClientOriginalExtension());
            //echo $extension;
            if ($extension == 'xml' or $extension == 'XML') {

                $leexml = $var;
                $bit2 = 1;
            }
	    
	        if($subido) {
	           $respuesta = array('respuesta' => "El Archivo subio con exito", 'ruta' => $file_name, 'archivo' => $archivo, 'leexml' => $leexml, 'bit2' => $bit2);
	        } else {
	           $respuesta = array('respuesta' => "Error al subir el archivo");
	        }
        } 

        return Response::json($respuesta);
}
}

public function consultaFolioFiscal($foliofiscal){

  $resultado = DB::table('OrdenPago')->select(DB::raw('count(*) as count'))->where('ORP_foliofiscal', '=', $foliofiscal)->get();
  $respuesta = array('count' => $resultado);

  return $resultado;
}

public function validaUnidad($rfc){

  $resultado = DB::table('Unidad')->select(DB::raw('UNI_rfc as rfc, UNI_claveWeb as unidadweb, UNI_claveint as unidad'))->where('UNI_rfc', '=', $rfc)->get();
  $respuesta = array('count' => $resultado);

  return $resultado;

}

public function ordenPago(){

		$folios = Input::all(); 

		foreach ($folios['seleccionados'] as $foliodato){

			$claveunidad = $foliodato['unidad'];

	    	$orden = Input::get['tipoorden'];
	    	$etapa = Input::get['etapa'];
	    	$foliofiscal = Input::get['foliofiscal'];
	    	$nombreEmisor = Input::get['emisor'];
	    	$rfcemisor = Input::get['rfc_emisor'];
	    	$total = Input::get['total'];
	    	$usuario = Input::get['usuarioentrega'];
	    	$factura = Input::get['foliointerno'];
	    	$serie = Input::get['serie'];
	    	$subtotal = Input::get['subtotal'];
	    	$descuento = Input::get['descuento'];
	    	$impuesto = Input::get['impuesto'];
	    	$tasa = Input::get['tasa'];
	    	$concepto = Input::get['concepto'];
	    	$tipotramite = Input::get['tipotramite'];

			$ordenpago = new OrdenPago;

			$ordenpago->TIO_id = $orden;
			$ordenpago->DOC_folio = $folio;
			$ordenpago->FLD_claveint = '';
			$ordenpago->DOC_claveint = '';
			$ordenpago->ORP_foliofiscal =  $foliofiscal;
			$ordenpago->ORP_nombreEmisor = $nombreEmisor;
			$ordenpago->ORP_rfcemisor = $rfcemisor;
			$ordenpago->ORP_importe = $subtotal;
			$ordenpago->ORP_iva = $tasa;
			$ordenpago->ORP_total= $total;
			$ordenpago->ORP_fechaemision = date('d/m/Y H:i:s'); 
			$ordenpago->ORP_usuregistro = $usuario;
			$ordenpago->ORP_factura = $factura;
			$ordenpago->ORP_serie = $serie;
			$ordenpago->ORP_descuento = $descuento;
			$ordenpago->ORP_impuesto = $impuesto;
			$ordenpago->TII_clave = '';
			$ordenpago->TCO_clave = '';

			$ordenpago->save();

			// $flujopagos = new Flujopagos;

			// $flujopagos->FLD_claveint = $claveflujo;
			// $flujopagos->PAS_folio = $folio;
			// $flujopagos->RPA_etapa = $etapa;
			// $flujopagos->RPA_numEnt = 1;

   //          $flujopagos->save();

			// Historial::altaEntrega($claveflujo);
	    }

		return Response::json(array('respuesta' => "Folio(s) Enviado"));

}

public function eliminaxml($usuario){

	$dir = "FacturasPagos/$usuario/";

        $handle = opendir($dir);
        $ficherosEliminados = 0;
        while ($file = readdir($handle)){
            if (is_file($dir.$file)){
                if (unlink($dir.$file) ){
                    $ficherosEliminados++;
                }
            }
        }

}

public function proveedor(){

	return Proveedor::where('PRV_activo','=', 1)
			->select('PRV_claveint as id','PRV_nombre as nombre', 'PRV_ref as referencia', 'PRV_rfc as rfc', 'PRV_persona as persona')
			->orderBy('PRV_nombre')
			->get();

}

}

