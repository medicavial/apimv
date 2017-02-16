<?php

class DetalleFacturasController extends BaseController {

	public function imprimeDatos($folio){

		$detalle = Expediente::join('Unidad', 'Expediente.Uni_clave', '=', 'Unidad.Uni_clave')
				->join('Compania','Expediente.Cia_clave', '=', 'Compania.Cia_clave')
				->join('DocumentosDigitales', 'Expediente.Exp_folio', '=', 'DocumentosDigitales.REG_folio')
				->select('Exp_folio as folio', 'Exp_completo as nombre', 'Cia_nombrecorto as cliente', 'Uni_nombrecorto as unidad',
		                  'Arc_archivo as ruta','Arc_clave as archivo','Expediente.Cia_clave as clave', 'Arc_cons as consecutivo', 'Expediente.Uni_clave as claveunidad')
				->where('Exp_folio', '=', $folio)
				// ->where('DocumentosDigitales.Arc_tipo','=',30)
				// ->where('DocumentosDigitales.Arc_tipo','=',29)
				->get();
		
		foreach ($detalle as $datos){

		    $url = "http://www.medicavial.net/registro/";
			$ruta = $datos['ruta'];
			$archivo = $datos['archivo'];
			$consecutivo = $datos['consecutivo'];
			$particion = explode('/', $ruta);
			$rut = $particion[0].'/'.$particion[1].'/'.$particion[2].'/'.$particion[3].'/'.$particion[4];
			$dir = $url.$rut.'/'.$archivo;

            $ext = explode('.',$archivo);
            $ext = end($ext);
            if ($ext == 'xml' or $ext == 'pdf' or $ext == 'XML' or $ext == 'PDF' or $ext == 'jpg' or $ext == 'JPG' or $ext == 'jpeg' or $ext == 'JPEG'){
                $archi[] = $archivo;
                $numero[] = $consecutivo;
            }
		}

	    $respuesta = array('respuesta' => $detalle, 'ruta' => $rut, 'archivo' => $archi, 'numero' => $numero);

 		return Response::json($respuesta);

 	}

 	public function rechazaFactura(){

 		$datos = Input::all();
 		$fecha = date('Y-m-d');

		foreach ($datos as $dato){

 			$folio = Input::get('folio'); 
 			$motivo = Input::get('motivo'); 
 			$usuario = Input::get('usuario'); 

			$inserta = DocumentoDigital::where('REG_folio', $folio)->where('Arc_tipo',30)->orWhere('Arc_tipo',29)->update(array('Arc_rechazado' => 1, 'USU_rechazo' => $usuario, 'Arc_motivo' => $motivo, 'Arc_fechaRechazo' => $fecha));

			$actualiza_estatus = Atenciones::where('Exp_folio', $folio)->update(array('ATN_estatus' => 6));

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

        $rfc = Unidad::where(array('UNI_claveWeb' => $unidad))->first()->UNI_rfc;
	                
        $xml = $datos['xml'];
        $pdf = $datos['pdf'];
        $ruta = $datos['ruta'];
        // $correo = 'entradacfd@medicavial.com.mx';
        $correo = "mvpagos@medicavial.com.mx";
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

