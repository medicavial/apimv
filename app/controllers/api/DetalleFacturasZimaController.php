<?php

class DetalleFacturasZimaController extends BaseController {

	public function imprimeDatos($folio){

		$detalle = PUFacturaRef::join('Unidad', 'PUFacturaRef.UNI_clave', '=', 'Unidad.UNI_clave')
	                        ->leftJoin('PUProductoZima', 'PUFacturaRef.Pro_clave', '=', 'PUProductoZima.ProdZima_Clave')
	                        ->join('PURegistro',  'PUFacturaRef.REG_folio', '=', 'PURegistro.REG_folio')
	                        ->join('Aseguradora',  'PURegistro.ASE_clave', '=', 'Aseguradora.ASE_clave')
	                        ->join('PUFacEstatusMV', 'PUFacturaRef.estatus', '=', 'PUFacEstatusMV.claveEstatus')
	                        ->where(array('PUFacturaRef.REG_folio' => $folio))
	                        ->select('PUFacturaRef.REG_folio as Folio','UNI_nomCorto as Unidad', 'REG_nombrecompleto as Lesionado',
	                        	     'ProdZima_Desc as Producto','REG_fechahora as FechaReg', 'SOL_clave as Solicitud', 
	                        	     'ASE_nomcorto as Aseguradora', 'estatus as estatus', 'descripcion as nombreestatus','rutaXml as rutaxml', 'rutapdf as rutapdf' )
	                        ->distinct()
	                        ->get();
     		
		foreach ($detalle as $datos){

		    $url = "http://pmzima.net/";
			$rutaxml = $datos['rutaxml'];
			$rutapdf = $datos['rutapdf'];

			$rutaxml = $url.$rutaxml;
			$rutapdf = $url.$rutapdf;

		}

	    $respuesta = array('respuesta' => $detalle, 'rutaxml' => $rutaxml, 'rutapdf' => $rutapdf);

 		return Response::json($respuesta);

 	}

 	public function rechazaFactura(){

 		$datos = Input::all();
 		$fecha = date('Y-m-d');

		foreach ($datos as $dato){

 			$folio = Input::get('folio'); 
 			$motivo = Input::get('motivo'); 
 			$usuario = Input::get('usuario'); 
 			$nsolicitud = Input::get('nsolicitud'); 

			$actualiza_estatus = PUFacturaRef::where('Exp_folio', $folio)->update(array('ATN_estatus' => 6));

 		}
 	    $respuesta = array('respuesta' => 'Factura Rechazada');

 		return Response::json($respuesta);

 	}

function revision(){

    // $mimemail = new nomad_mimemail();

    $dia = time()-(2*24*60*60); //Te resta un dia (2*24*60*60) te resta dos y //asi...
	$dia_fin = date('d-m-Y', $dia); //Formatea dia
	$fecha = date("d-m-Y");

	$datos = Input::all();

	$folio = $datos['folio'];



    Mail::send('emails.auth.reminder', array($datos), function($message) use ($datos)
	{

		$folio = $datos['folio'];
        $unidad = $datos['unidad'];

        $rfc = UnidadZima::where(array('UNI_claveWeb' => $unidad))->first()->UNI_rfc;
	                
        $xml = $datos['xml'];
        $pdf = $datos['pdf'];
        $ruta = $datos['ruta'];
        // $correo = 'entradacfd@medicavial.com.mx';
        $correo = "adominguez@medicavial.com.mx";
        $asunto = $folio.'-'.$rfc;

        // $copias = explode(',', $copias);
 
        $message->from('mvpagos@medicavial.com.mx', 'Revision de Facturas');
        $message->subject($asunto);
        $message->to($correo);
        // foreach ($copias as  $copia) {
        // 	$message->cc($copia);
        // 	# code...
        // }
        $archivo =  'http://medicavial.net/registro/'.$ruta.'/'.$xml;
        $archivo1 =  'http://medicavial.net/registro/'.$ruta.'/'.$pdf;
            //archivos adjuntos para el correo
        $message->attach($archivo);
        $message->attach($archivo1);

	});

        $actualiza = Atenciones::where('Exp_folio', $folio)->update(array('ATN_estatusrevision' => 1));



	$respuesta = array('respuesta' => 'Tu Factura fue enviada a Revisión');

 	return Response::json($respuesta);

	}   

}

