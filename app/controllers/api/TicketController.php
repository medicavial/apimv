<?php

class TicketController extends BaseController {
	// consulta de tickets registrados

	public function actualizaticket(){

		$foliointerno  = Input::get('folioIn');
		$folioweb  = Input::get('folioweb');
		$etapa  = Input::get('etapa');
		$categoria  = Input::get('categoria');
		$subcategoria  = Input::get('subcategoria');
		$status  = Input::get('statusa');
		$asignado  = Input::get('asignado');
		$fechaasignado  = Input::get('fechaasignado');
		$notas  = Input::get('notas');
		$comunicacion  = Input::get('comunicacion');
		$fechacomunica  = Input::get('fechacomunica');
		$usuario  = Input::get('usuario');
		$usuariomv  = Input::get('usuariomv');

		$fechaasignado =  date('Y-m-d', strtotime(str_replace('/', '-', $fechaasignado))) . '00:00:00';
		$fechacomunica =  date('Y-m-d', strtotime(str_replace('/', '-', $fechacomunica))) . '00:00:00';

		$ticket =  Tickets::find($foliointerno);

		$ticket->TCat_clave = $categoria;
		$ticket->TSub_clave = $subcategoria;
		$ticket->TStatus_clave = $status;
		$ticket->TSeg_asignado = $asignado;
		$ticket->TSeg_asignadofecha = $fechaasignado;
		$ticket->TSeg_fechaactualizacion = date('Y-m-d H:i:s');
		$ticket->Usu_actualiza = $usuario;

		$ticket->save();

		if($notas<>""){

			$nota = new Ticketnotas;

            $nota->TSeg_clave =  $foliointerno;
            $nota->Exp_folio =  $folioweb;
            $nota->TN_descripcion =  $notas;
            $nota->TN_fechareg =  date('Y-m-d H:i:s');
            $nota->Usu_registro =  $usuario;

            $nota->save();
            
        }
        
        if($comunicacion<>""){

        	$comunica = new Ticketcomunicacion;
            
            $comunica->TSeg_clave =  $foliointerno;
            $comunica->Exp_folio =  $folioweb;
            $comunica->TC_descripcion =  $comunicacion;
            $comunica->TC_correofecha =  $fechacomunica;
            $comunica->TC_fechareg =  date('Y-m-d H:i:s');
            $comunica->Usu_registro =  $usuario;

            $comunica->save();

        }

        $respuesta = array('respuesta' => "Los Datos del Ticket Se Actualizaron Correctamente");

        return $respuesta;

	}

	public function altaticket(){

		$folioin  = Input::get('folioIn');
		$folioweb  = Input::get('folioweb');
		$cliente  = Input::get('cliente');
		$unidad  = Input::get('unidad');
		$etapa  = Input::get('etapa');
		$categoria  = Input::get('categoria');
		$subcategoria  = Input::get('subcategoria');
		$status  = Input::get('statusa');
		$asignado  = Input::get('asignado');
		$fechaasignado  = Input::get('fechaasignado');
		$notas  = Input::get('notas');
		$comunicacion  = Input::get('comunicacion');
		$fechacomunica  = Input::get('fechacomunica');
		$usuario  = Input::get('usuario');
		$usuariomv  = Input::get('usuariomv');

		$fecha=date('Y-m-d H:i:s');

		$fechaasignado =  date('Y-m-d', strtotime(str_replace('/', '-', $fechaasignado))) . '00:00:00';
		$fechacomunica =  date('Y-m-d', strtotime(str_replace('/', '-', $fechacomunica))) . '00:00:00';

		$observaciones = array();

		if (Input::get('diagnostico')) {
			array_push($observaciones,'DIAGNÓSTICO');
		};

		if (Input::get('firma')) {
			array_push($observaciones,'FIRMA DE LESIONADO');
		};

		if (Input::get('notamedica')) {
			array_push($observaciones,'FALTA NOTA MÉDICA');
		};

		if (Input::get('finiquito')) {
			array_push($observaciones,'SIN FINIQUITO');
		};

		if (Input::get('refactura')) {
			array_push($observaciones,'REFACTURACIÓN');
		};

		if (Input::get('pase')) {
			array_push($observaciones,'PASE MÉDICO ALTERADO'); 
		};

		if (Input::get('suministro')) {
			array_push($observaciones,'SUMINISTROS (MEDICAMENTO/ ÓRTESIS)');
		};

		if (Input::get('identificacion')) {
			array_push($observaciones,'IDENTIFICACIÓN OFICIAL');
		};

		if (Input::get('verificacion')) {
			array_push($observaciones,'VERIFICACIÓN DE FIRMAS');
		};

		if (Input::get('notamedicain')) {
			array_push($observaciones,'NOTA MÉDICA INCOMPLETA');
		};

		if (Input::get('informe')) {
			array_push($observaciones,'INFORME AIG/CHARTI');
		};

		if (Input::get('reverso')) {
			array_push($observaciones,'REVERSO DE IDENTIFICACIÓN');
		};

		if (Input::get('verificapar')) {
			array_push($observaciones,'VERIFICACIÓN DE PARENTESCO');
		};

		if (Input::get('nocoincide')) {
			array_push($observaciones,'NO COINCIDE CON DOCUMENTACIÓN');
		};

		if (Input::get('pasemedico')) {
			array_push($observaciones,'PASE MÉDICO EN COPIA');
		};

		if (Input::get('nombrein')) {
			array_push($observaciones,'NOMBRE INCORRECTO');
		};

		if (Input::get('folioseg')) {
			array_push($observaciones,'FOLIO DE SEGMENTACIÓN');
		};

		if (Input::get('sinpase')) {
			array_push($observaciones,'SIN PASE MÉDICO');
		};

		if (Input::get('fueravigencia')) {
			array_push($observaciones,'FUERA DE VIGENCIA');
		};

		if (Input::get('sinpoliza')) {
			array_push($observaciones,'SIN PÓLIZA');
		};

		if (Input::get('sindeducible')) {
			array_push($observaciones,'SIN DEDUCIBLE');
		};

		if (Input::get('sincuestionario')) {
			array_push($observaciones,'SIN CUESTIONARIO');
		};

		if (Input::get('firmamedico')) {
			array_push($observaciones,'FIRMA MÉDICO');
		};

		if (Input::get('cruce')) {
			array_push($observaciones,'CRUCE ZIMA-MV');
		};

		if (Input::get('cedulaT')) {
			array_push($observaciones,'CÉDULA EN TRAMITE');
		};

		if (Input::get('cedulaI')) {
			array_push($observaciones,'CÉDULA INVALIDA');
		};

		$observaciones=implode(",",$observaciones);


		$ticket = new Tickets;

		$ticket->TSeg_clave = $folioin;
		$ticket->Exp_folio = $folioweb;
		$ticket->TSeg_etapa = $etapa;
		$ticket->TCat_clave = $categoria;
		$ticket->TSub_clave = $subcategoria;
		$ticket->TSeg_obs = $observaciones;
		$ticket->TStatus_clave = $status;
		$ticket->Uni_clave = $unidad;
		$ticket->Cia_clave = $cliente;
		$ticket->TSeg_asignado = $asignado;
		$ticket->TSeg_asignadofecha = $fechaasignado;
		$ticket->TSeg_fechareg = date('Y-m-d H:i:s');
		$ticket->TSeg_fechaactualizacion = date('Y-m-d H:i:s');
		$ticket->Usu_registro = $usuario;
		$ticket->Usu_actualiza = $usuario;

		$ticket->save();

		if($notas<>""){

			$nota = new Ticketnotas;

            $nota->TSeg_clave =  $folioin;
            $nota->Exp_folio =  $folioweb;
            $nota->TN_descripcion =  $notas;
            $nota->TN_fechareg =  date('Y-m-d H:i:s');
            $nota->Usu_registro =  $usuario;

            $nota->save();
            
        }
        
        if($comunicacion<>""){

        	$comunica = new Ticketcomunicacion;
            
            $comunica->TSeg_clave =  $folioin;
            $comunica->Exp_folio =  $folioweb;
            $comunica->TC_descripcion =  $comunicacion;
            $comunica->TC_correofecha =  $fechacomunica;
            $comunica->TC_fechareg =  date('Y-m-d H:i:s');
            $comunica->Usu_registro =  $usuario;

            $comunica->save();

        }

        $respuesta = array('respuesta' => "Los Datos del Ticket Se Guardaron Correctamente con el folio:". $folioin);

        // Historial::altaTicket($folioweb,$folioin,$etapa,$usuariomv);

        return $respuesta;

	}

	public function status(){
		
		return Status::select('TStatus_clave as Clave', 'TStatus_nombre as Nombre')->get();

	}

	public function categorias(){
		
		return Categorias::select('TCat_clave as Clave', 'TCat_nombre as Nombre')->get();

	}

	public function consulta(){
		
		$fechaini =  DateTime::createFromFormat('d/m/Y', Input::get('fechaini') )->format('Y-m-d') . ' 00:00:00';
		$fechafin =  DateTime::createFromFormat('d/m/Y', Input::get('fechafin') )->format('Y-m-d') . ' 23:59:59';

		$tickets = Tickets::fecha($fechaini,$fechafin);

		return $tickets;

	}

	public function detalle($foliointerno,$folio){

		$datos = array();
		
		$ticket = Tickets::find($foliointerno);
		$notas = Ticketnotas::where('TSeg_clave','=',$foliointerno)->get();
		$comunicacion = Ticketcomunicacion::where('TSeg_clave','=',$foliointerno)->get(); 

		$expediente = FolioWeb::join('Compania', 'Compania.CIA_clave', '=', 'Expediente.CIA_clave')
				->join('Unidad', 'Unidad.UNI_clave', '=', 'Expediente.UNI_clave')
				->join('Producto', 'Producto.Pro_clave', '=', 'Expediente.Pro_clave')
				->leftjoin('Escolaridad', 'Escolaridad.ESC_clave', '=', 'Expediente.ESC_clave')
				->where('Exp_folio','=', $folio)
				->first();

		$datos['expediente'] = $expediente;
        $datos['ticket'] = $ticket;
        $datos['notas'] = $notas;
        $datos['comunicacion'] = $comunicacion;

        return $datos;

	}

	public function folio($folio){

		$tickets = Tickets::folio($folio);

		return $tickets;

	}

	public function foliointerno($folio){
		
		$tickets = Tickets::interno($folio);

		return $tickets;

	}

	public function maximo(){
		
		return Tickets::max('TSeg_clave');

	}

	public function subcategorias($categoria){
		
		return Subcategorias::where('TCat_clave','=',$categoria)
							->select('TSub_clave as Clave','TSub_nombre as Nombre')
							->get();

	}
	
	/// tickets de pagos aqui 


	public function actualizaticketpagos(){

		$foliointerno  = Input::get('folioIn');
		$folioweb  = Input::get('folioweb');
		$etapa  = Input::get('etapa');
		$categoria  = Input::get('categoria');
		$subcategoria  = Input::get('subcategoria');
		$status  = Input::get('statusa');
		$asignado  = Input::get('asignado');
		$fechaasignado  = Input::get('fechaasignado');
		$notas  = Input::get('notas');
		$comunicacion  = Input::get('comunicacion');
		$fechacomunica  = Input::get('fechacomunica');
		$usuario  = Input::get('usuario');
		$usuariomv  = Input::get('usuariomv');
		$entrega  = Input::get('entrega');
		$factura = Input::get('factura');

		$fechaasignado =  date('Y-m-d', strtotime(str_replace('/', '-', $fechaasignado))) . '00:00:00';
		$fechacomunica =  date('Y-m-d', strtotime(str_replace('/', '-', $fechacomunica))) . '00:00:00';

		$ticket =  Ticketpagos::find($foliointerno);

		$ticket->TCat_clave = $categoria;
		$ticket->TSub_clave = $subcategoria;
		$ticket->TStatus_clave = $status;
		$ticket->TSeg_asignado = $asignado;
		$ticket->TSeg_asignadofecha = $fechaasignado;
		$ticket->TSeg_fechaactualizacion = date('Y-m-d H:i:s');
		$ticket->Usu_actualiza = $usuario;
		$ticket->TSeg_entrega = $entrega;
		$ticket->FAC_claveint = $factura;

		$ticket->save();

		if($notas<>""){

			$nota = new Ticketnotaspagos;

            $nota->TSeg_clave =  $foliointerno;
            $nota->Exp_folio =  $folioweb;
            $nota->TN_descripcion =  $notas;
            $nota->TN_fechareg =  date('Y-m-d H:i:s');
            $nota->Usu_registro =  $usuario;

            $nota->save();
            
        }
       

        $respuesta = array('respuesta' => "Los Datos del Ticket Se Actualizaron Correctamente");

        return $respuesta;

	}

	public function altaticketpagos(){

		$folioweb  = Input::get('folioweb');
		$cliente  = Input::get('cliente');
		$unidad  = Input::get('unidad');
		$etapa  = Input::get('etapa');
		$entrega  = Input::get('entrega');
		$categoria  = Input::get('categoria');
		$subcategoria  = Input::get('subcategoria');
		$status  = Input::get('statusa');
		$asignado  = Input::get('asignado');
		$fechaasignado  = Input::get('fechaasignado');
		$notas  = Input::get('notas');
		$comunicacion  = Input::get('comunicacion');
		$fechacomunica  = Input::get('fechacomunica');
		$usuario  = Input::get('usuario');
		$usuariomv  = Input::get('usuariomv');
		$observaciones = Input::get('observaciones');
		$factura = Input::get('factura');

		$fecha=date('Y-m-d H:i:s');

		$fechaasignado =  date('Y-m-d', strtotime(str_replace('/', '-', $fechaasignado))) . '00:00:00';
		$fechacomunica =  date('Y-m-d', strtotime(str_replace('/', '-', $fechacomunica))) . '00:00:00';

		$ticket = new Ticketpagos;

		$ticket->Exp_folio = $folioweb;
		$ticket->TSeg_etapa = $etapa;
		$ticket->TSeg_entrega = $entrega;
		$ticket->TCat_clave = $categoria;
		$ticket->TSub_clave = $subcategoria;
		$ticket->TSeg_obs = $observaciones;
		$ticket->TStatus_clave = $status;
		$ticket->Uni_clave = $unidad;
		$ticket->Cia_clave = $cliente;
		$ticket->TSeg_asignado = $asignado;
		$ticket->TSeg_asignadofecha = $fechaasignado;
		$ticket->TSeg_fechareg = date('Y-m-d H:i:s');
		$ticket->TSeg_fechaactualizacion = date('Y-m-d H:i:s');
		$ticket->Usu_registro = $usuario;
		$ticket->Usu_actualiza = $usuario;
		$ticket->FAC_claveint = $factura;

		$ticket->save();

		$folioin = $ticket->TSeg_clave;

		if($notas<>""){

			$nota = new Ticketnotaspagos;

            $nota->TSeg_clave =  $folioin;
            $nota->Exp_folio =  $folioweb;
            $nota->TN_descripcion =  $notas;
            $nota->TN_fechareg =  date('Y-m-d H:i:s');
            $nota->Usu_registro =  $usuario;

            $nota->save();
            
        }

        $respuesta = array('respuesta' => "Los Datos del Ticket Se Guardaron Correctamente con el folio:". $folioin, 'foliointerno' => $folioin);

        // Historial::altaTicket($folioweb,$folioin,$etapa,$usuariomv);

        return $respuesta;

	}

	public function statuspagos(){
		
		return Statuspagos::select('TStatus_clave as Clave', 'TStatus_nombre as Nombre')->get();

	}

	public function categoriaspagos(){
		
		return Categoriaspagos::select('TCat_clave as Clave', 'TCat_nombre as Nombre')->get();

	}

	public function consultapagos(){
		
		$fechaini =  DateTime::createFromFormat('d/m/Y', Input::get('fechaini') )->format('Y-m-d') . ' 00:00:00';
		$fechafin =  DateTime::createFromFormat('d/m/Y', Input::get('fechafin') )->format('Y-m-d') . ' 23:59:59';

		$tickets = Ticketpagos::leftJoin('TicketPagosCat', 'TicketPagosCat.TCat_clave', '=', 'TicketPagos.TCat_clave')
				->leftJoin('TicketPagosSubcat',  'TicketPagosSubcat.TSub_clave', '=', 'TicketPagos.TSub_clave')
				->leftJoin('TicketPagosStatus', 'TicketPagosStatus.TStatus_clave', '=', 'TicketPagos.TStatus_clave')
				->leftJoin('Expediente', 'Expediente.Exp_folio', '=', 'TicketPagos.Exp_folio')
				->leftJoin('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->leftJoin('Compania', 'Compania.Cia_clave', '=', 'TicketPagos.Cia_clave')
				->leftJoin('Usuario', 'Usuario.Usu_login', '=', 'TicketPagos.Usu_registro')
				->select(DB::raw('TSeg_clave as Folio_Interno, TicketPagos.Exp_folio as Folio_Web, TSeg_etapa as Etapa,TSeg_entrega as Entrega, FAC_claveint as Factura, TCat_nombre as Categoria, TSub_nombre as Subcategoria,
									TStatus_nombre as Status, TSeg_obs as Observaciones, Uni_nombre as Unidad, TSeg_Asignado as Asignado, TSeg_fechareg as Registro,
									Usu_nombre as Usuario_Registro, Tseg_fechaactualizacion as Ultima_Actualizacion, CONCAT(Exp_nombre," ", Exp_paterno," ", Exp_materno) As Lesionado,Cia_nombrecorto as Cliente,
									TicketPagos.Cia_clave, Usuario.Usu_login, TicketPagos.Uni_clave,TicketPagos.TStatus_clave'))
				->whereBetween('TSeg_fechareg', array($fechaini, $fechafin))
				->orderBy('TSeg_clave')
				->get();

		return $tickets;

	}

	public function detallepagos($foliointerno,$folio){
		$datos = array();
		
		$ticket = Ticketpagos::find($foliointerno);
		$notas = Ticketnotaspagos::where('TSeg_clave','=',$foliointerno)->get();
		

		$expediente = FolioWeb::join('Compania', 'Compania.CIA_clave', '=', 'Expediente.CIA_clave')
				->join('Unidad', 'Unidad.UNI_clave', '=', 'Expediente.UNI_clave')
				->join('Producto', 'Producto.Pro_clave', '=', 'Expediente.Pro_clave')
				->leftjoin('Escolaridad', 'Escolaridad.ESC_clave', '=', 'Expediente.ESC_clave')
				->where('Exp_folio','=', $folio)
				->first();

		$datos['expediente'] = $expediente;
        $datos['ticket'] = $ticket;
        $datos['notas'] = $notas;

        return $datos;
	}

	public function foliopagos($folio){

		$tickets = Ticketpagos::leftJoin('TicketPagosCat', 'TicketPagosCat.TCat_clave', '=', 'TicketPagos.TCat_clave')
				->leftJoin('TicketPagosSubcat',  'TicketPagosSubcat.TSub_clave', '=', 'TicketPagos.TSub_clave')
				->leftJoin('TicketPagosStatus', 'TicketPagosStatus.TStatus_clave', '=', 'TicketPagos.TStatus_clave')
				->leftJoin('Expediente', 'Expediente.Exp_folio', '=', 'TicketPagos.Exp_folio')
				->leftJoin('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->leftJoin('Compania', 'Compania.Cia_clave', '=', 'TicketPagos.Cia_clave')
				->leftJoin('Usuario', 'Usuario.Usu_login', '=', 'TicketPagos.Usu_registro')
				->select(DB::raw('TSeg_clave as Folio_Interno, TicketPagos.Exp_folio as Folio_Web, TSeg_etapa as Etapa,TSeg_entrega as Entrega, FAC_claveint as Factura, TCat_nombre as Categoria, TSub_nombre as Subcategoria,
									TStatus_nombre as Status, TSeg_obs as Observaciones, Uni_nombre as Unidad, TSeg_Asignado as Asignado, TSeg_fechareg as Registro,
									Usu_nombre as Usuario_Registro, Tseg_fechaactualizacion as Ultima_Actualizacion, CONCAT(Exp_nombre," ", Exp_paterno," ", Exp_materno) As Lesionado,Cia_nombrecorto as Cliente,
									TicketPagos.Cia_clave, Usuario.Usu_login, TicketPagos.Uni_clave,TicketPagos.TStatus_clave'))
				->where('TicketPagos.Exp_folio', $folio)
				->get();

		return $tickets;
	}

	public function foliointernopagos($folio){
		
		$tickets = Ticketpagos::leftJoin('TicketPagosCat', 'TicketPagosCat.TCat_clave', '=', 'TicketPagos.TCat_clave')
				->leftJoin('TicketPagosSubcat',  'TicketPagosSubcat.TSub_clave', '=', 'TicketPagos.TSub_clave')
				->leftJoin('TicketPagosStatus', 'TicketPagosStatus.TStatus_clave', '=', 'TicketPagos.TStatus_clave')
				->leftJoin('Expediente', 'Expediente.Exp_folio', '=', 'TicketPagos.Exp_folio')
				->leftJoin('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->leftJoin('Compania', 'Compania.Cia_clave', '=', 'TicketPagos.Cia_clave')
				->leftJoin('Usuario', 'Usuario.Usu_login', '=', 'TicketPagos.Usu_registro')
				->select(DB::raw('TSeg_clave as Folio_Interno, TicketPagos.Exp_folio as Folio_Web, TSeg_etapa as Etapa,TSeg_entrega as Entrega, FAC_claveint as Factura, TCat_nombre as Categoria, TSub_nombre as Subcategoria,
									TStatus_nombre as Status, TSeg_obs as Observaciones, Uni_nombre as Unidad, TSeg_Asignado as Asignado, TSeg_fechareg as Registro,
									Usu_nombre as Usuario_Registro, Tseg_fechaactualizacion as Ultima_Actualizacion, CONCAT(Exp_nombre," ", Exp_paterno," ", Exp_materno) As Lesionado,Cia_nombrecorto as Cliente,
									TicketPagos.Cia_clave, Usuario.Usu_login, TicketPagos.Uni_clave,TicketPagos.TStatus_clave'))
				->where('TSeg_clave',$folio)
				->get();

		return $tickets;

	}

	public function subcategoriaspagos($categoria){
		
		return Subcategoriaspagos::where('TCat_clave','=',$categoria)
							->select('TSub_clave as Clave','TSub_nombre as Nombre')
							->get();

	}

}
