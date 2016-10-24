<?php

class Historial {


	//parametros iniciales
	public $titulo;
	public $descripcion;
	public $folio;
	public $usuario;
	public $etapa;
	public $entrega;

	// funcion para guardar en historial web del SACE
	public function guardar(){

		$Historial = new HistorialWeb;
		$Historial->HIS_fecha = date('Y-m-d H:i:s');
		$Historial->HIS_titulo = $this->titulo;
		$Historial->HIS_descripcion = $this->descripcion;
		$Historial->Exp_folio = $this->folio;
		$Historial->Exp_etapa = $this->etapa;
		$Historial->Exp_entrega = $this->entrega;
		$Historial->USU_login = $this->usuario;
		$Historial->save();
	}

    public static function altaOriginal($folio,$etapa,$entrega)
    {

        $datos = Documento::where('DOC_folio',$folio)->where('DOC_etapa',$etapa)->where('DOC_numeroEntrega',$entrega)->get();
		foreach ($datos as $dato) {
			$documento = $dato['DOC_claveint'];
		}

		$flujo = Flujo::where('DOC_claveint',$documento)->first();
		$area = $flujo->ARO_activa;
		$usuario = $flujo->USU_activo;
		$claveflujo = $flujo->FLD_claveint;
		$nombre = User::find($usuario)->USU_nombre;

		if($etapa == 1){
    		$descripcion =  "Se capturó la primera atención del folio por el usuario " . $nombre;
    	}elseif($etapa == 2){
    		$descripcion = "Se emitio Subsecuencia del folio por el usuario ". $nombre;
    	}else{
    		$descripcion =  "Se emitio Rehabilitación del folio por el usuario " . $nombre;
    	} 

		$historial = new Historico;
		$historial->HIS_folio = $folio;
		$historial->HIS_fecha =  date('d/m/Y H:i:s'); 
		$historial->HIS_hora =  date('d/m/Y H:i:s'); 
		$historial->HIS_titulo =  'Se emitio Original'; //indica que se ingreso un nuevo expediente en recepcion de documentos
		$historial->HIS_descripcion =  $descripcion; 
		$historial->HIS_area =  $area;
		$historial->HIS_usuario =  $usuario;
		$historial->HIS_accion = 'Original';
		$historial->HIS_etapa =  $etapa;
		$historial->HIS_entrega =  $entrega;
		$historial->DOC_claveint =  $documento;  
		$historial->FLD_claveint =  $claveflujo;
		$historial->save();
		
		return  true;

    }



    public static function altaTicket($folio,$foliointerno,$etapa,$usuario)
    {

        $datos = Documento::where('DOC_folio',$folio)->where('DOC_etapa',$etapa)->get();
		foreach ($datos as $dato) {
			$documento = $dato['DOC_claveint'];
		}

		$flujo = Flujo::where('DOC_claveint',$documento)->first();
		$claveflujo = $flujo->FLD_claveint;

		$usuarios = DB::table('UsuarioArea')
		->where('USU_claveint', 1)
		->get();

		foreach ($usuarios as $user => $value) {
			$area = $value->ARO_claveint;
		}

		$areaNombre = Areas::find($area)->ARO_nombre;
		$nombre = User::find($usuario)->USU_nombre;

    	$descripcion =  "El Usuario " . $nombre . " del area " . $areaNombre . " emitió ticket con folio interno " . $foliointerno . "";

		$historial = new Historico;
		$historial->HIS_folio = $folio;
		$historial->HIS_fecha =  date('d/m/Y H:i:s'); 
		$historial->HIS_hora =  date('d/m/Y H:i:s'); 
		$historial->HIS_titulo =  'Se emitio Ticket'; //indica que se ingreso un nuevo expediente en recepcion de documentos
		$historial->HIS_descripcion =  $descripcion; 
		$historial->HIS_area =  $area;
		$historial->HIS_usuario =  $usuario;
		$historial->HIS_accion = 'Ticket';
		$historial->HIS_etapa =  $etapa;
		$historial->HIS_entrega =  0;
		$historial->DOC_claveint =  $documento;  
		$historial->FLD_claveint =  $claveflujo;
		$historial->save();
		
		return  true;

    }



    public static function altaEntrega($clave)
    {

		$flujo = Flujo::find($clave);

		$area = $flujo->ARO_activa;
		$usuario = $flujo->USU_activo;
		$documento = $flujo->DOC_claveint;
		$nombre = User::find($usuario)->USU_nombre;

		$doc = Documento::find($documento);

		$folio = $doc->DOC_folio;
		$etapa = $doc->DOC_etapa;
		$entrega = $doc->DOC_numeroEntrega;

		$usuarioentrega = User::find($flujo->USU_ent)->USU_nombre;
		$areaentrega = Areas::find($flujo->FLD_AROEnt)->ARO_nombre;
		$arearecibe = Areas::find($flujo->ARO_porRecibir)->ARO_nombre;
		$usuariorecibe = User::find($flujo->USU_recibe)->USU_nombre;

		if ($flujo->ARO_porRecibir == 5) {
			$detalle = " el juego de facturación en espera de ser aceptado";
		}elseif ($arearecibe == 7) {
			$detalle = " con autorecepcion del documento";
		}elseif ($arearecibe == 8) {
			$detalle = " se asigno por motivo de: " . $observaciones;
		}else{
			$detalle = " en espera de ser aceptado";
		}
		
		$titulo = "El Usuario " . $usuarioentrega . " generó una entrega";
		$descripcion = "Se entregó el documento del area de " . $areaentrega . " al area de " . $arearecibe . " al usuario: " . $usuariorecibe . $detalle;

		$historial = new Historico;
		$historial->HIS_folio = $folio;
		$historial->HIS_fecha =  date('d/m/Y H:i:s'); 
		$historial->HIS_hora =  date('d/m/Y H:i:s'); 
		$historial->HIS_titulo =  $titulo; //indica que se ingreso un nuevo expediente en recepcion de documentos
		$historial->HIS_descripcion =  $descripcion; 
		$historial->HIS_area =  $area;
		$historial->HIS_usuario =  $usuario;
		$historial->HIS_accion = 'Entrega';
		$historial->HIS_etapa =  $etapa;
		$historial->HIS_entrega =  $entrega;
		$historial->DOC_claveint =  $documento;  
		$historial->FLD_claveint =  $clave;
		$historial->save();
		
		return  true;

    }


    public static function altaNPC($clave)
    {

		$flujo = Flujo::find($clave);

		$area = $flujo->ARO_activa;
		$usuario = $flujo->USU_activo;
		$documento = $flujo->DOC_claveint;
		$nombre = User::find($usuario)->USU_nombre;

		$doc = Documento::find($documento);

		$folio = $doc->DOC_folio;
		$etapa = $doc->DOC_etapa;
		$entrega = $doc->DOC_numeroEntrega;

		$usuarionombre = User::find($usuario)->USU_nombre;
		
		$titulo = "Se Envio a No pagar hasta cobrar";
		$descripcion = "El Folio se movio a no pagar hasta cobrar por el usuario: " . $usuarionombre;

		$historial = new Historico;
		$historial->HIS_folio = $folio;
		$historial->HIS_fecha =  date('d/m/Y H:i:s'); 
		$historial->HIS_hora =  date('d/m/Y H:i:s'); 
		$historial->HIS_titulo =  $titulo; //indica que se ingreso un nuevo expediente en recepcion de documentos
		$historial->HIS_descripcion =  $descripcion; 
		$historial->HIS_area =  $area;
		$historial->HIS_usuario =  $usuario;
		$historial->HIS_accion = 'Entrega';
		$historial->HIS_etapa =  $etapa;
		$historial->HIS_entrega =  $entrega;
		$historial->DOC_claveint =  $documento;  
		$historial->FLD_claveint =  $clave;
		$historial->save();
		
		return  true;

    }


    public static function aceptaEntrega($clave)
    {

		$flujo = Flujo::find($clave);

		$documento = $flujo->DOC_claveint;

		$doc = Documento::find($documento);

		$folio = $doc->DOC_folio;
		$etapa = $doc->DOC_etapa;
		$entrega = $doc->DOC_numeroEntrega;

		$usuarioentrega = User::find($flujo->USU_ent)->USU_nombre;
		$areaentrega = Areas::find($flujo->FLD_AROEnt)->ARO_nombre;
		$arearecibe = Areas::find($flujo->ARO_porRecibir)->ARO_nombre;
		$usuariorecibe = User::find($flujo->USU_recibe)->USU_nombre;

		$nombre = User::find($flujo->USU_recibe)->USU_nombre;

		if ($arearecibe == 4) {
			$detalle = " -- juego de facturación --";
		}else{
			$detalle = '';
		}

		$titulo = "El Usuario " . $usuariorecibe . " aceptó entrega";
		$descripcion = "Se aceptó el documento mandado por el area de " . $areaentrega . " por el usuario: " . $usuariorecibe . $detalle;

		$historial = new Historico;
		$historial->HIS_folio = $folio;
		$historial->HIS_fecha =  date('d/m/Y H:i:s'); 
		$historial->HIS_hora =  date('d/m/Y H:i:s'); 
		$historial->HIS_titulo =  $titulo; //indica que se ingreso un nuevo expediente en recepcion de documentos
		$historial->HIS_descripcion =  $descripcion; 
		$historial->HIS_area =  $flujo->ARO_porRecibir;
		$historial->HIS_usuario =  $flujo->USU_recibe;
		$historial->HIS_accion = 'Recepcion';
		$historial->HIS_etapa =  $etapa;
		$historial->HIS_entrega =  $entrega;
		$historial->DOC_claveint =  $documento;  
		$historial->FLD_claveint =  $clave;
		$historial->save();
		
		return  true;

    }

    public static function bajaNPC($clave)
    {

		$flujo = Flujo::find($clave);

		$area = $flujo->ARO_activa;
		$usuario = $flujo->USU_activo;
		$documento = $flujo->DOC_claveint;
		$nombre = User::find($usuario)->USU_nombre;

		$doc = Documento::find($documento);

		$folio = $doc->DOC_folio;
		$etapa = $doc->DOC_etapa;
		$entrega = $doc->DOC_numeroEntrega;

		$usuarionombre = User::find($usuario)->USU_nombre;
		
		$titulo = "Se Quito de No pagar hasta cobrar";
		$descripcion = "El Folio se quito de No pagar hasta cobrar por el usuario: " . $usuarionombre;

		$historial = new Historico;
		$historial->HIS_folio = $folio;
		$historial->HIS_fecha =  date('d/m/Y H:i:s'); 
		$historial->HIS_hora =  date('d/m/Y H:i:s'); 
		$historial->HIS_titulo =  $titulo; //indica que se ingreso un nuevo expediente en recepcion de documentos
		$historial->HIS_descripcion =  $descripcion; 
		$historial->HIS_area =  $area;
		$historial->HIS_usuario =  $usuario;
		$historial->HIS_accion = 'Entrega';
		$historial->HIS_etapa =  $etapa;
		$historial->HIS_entrega =  $entrega;
		$historial->DOC_claveint =  $documento;  
		$historial->FLD_claveint =  $clave;
		$historial->save();
		
		return  true;

    }

    public static function quitaEntrega($clave)
    {

		$flujo = Flujo::find($clave);

		$area = $flujo->ARO_activa;
		$usuario = $flujo->USU_activo;
		$documento = $flujo->DOC_claveint;
		$nombre = User::find($usuario)->USU_nombre;

		$doc = Documento::find($documento);

		$folio = $doc->DOC_folio;
		$etapa = $doc->DOC_etapa;
		$entrega = $doc->DOC_numeroEntrega;

		$usuarioentrega = User::find($flujo->USU_ent)->USU_nombre;
		$areaentrega = Areas::find($flujo->FLD_AROEnt)->ARO_nombre;
		$arearecibe = Areas::find($flujo->ARO_porRecibir)->ARO_nombre;
		$usuariorecibe = User::find($flujo->USU_recibe)->USU_nombre;
		
		$titulo = "Se quito como entrega de documento";
		$descripcion = "El usuario " . $usuarioentrega . "  removio el documento impidiendo que llegara al area: " . $arearecibe;

		$historial = new Historico;
		$historial->HIS_folio = $folio;
		$historial->HIS_fecha =  date('d/m/Y H:i:s'); 
		$historial->HIS_hora =  date('d/m/Y H:i:s'); 
		$historial->HIS_titulo =  $titulo; //indica que se ingreso un nuevo expediente en recepcion de documentos
		$historial->HIS_descripcion =  $descripcion; 
		$historial->HIS_area =  $area;
		$historial->HIS_usuario =  $usuario;
		$historial->HIS_accion = 'Entrega';
		$historial->HIS_etapa =  $etapa;
		$historial->HIS_entrega =  $entrega;
		$historial->DOC_claveint =  $documento;  
		$historial->FLD_claveint =  $clave;
		$historial->save();
		
		return  true;

    }


    public static function rechazaEntrega($clave, $motivo)
    {

		$flujo = Flujo::find($clave);

		$documento = $flujo->DOC_claveint;

		$doc = Documento::find($documento);

		$folio = $doc->DOC_folio;
		$etapa = $doc->DOC_etapa;
		$entrega = $doc->DOC_numeroEntrega;

		$usuarioentrega = User::find($flujo->USU_ent)->USU_nombre;
		$areaentrega = Areas::find($flujo->FLD_AROEnt)->ARO_nombre;
		$arearecibe = Areas::find($flujo->ARO_porRecibir)->ARO_nombre;
		$usuariorecibe = User::find($flujo->USU_recibe)->USU_nombre;

		$nombre = User::find($flujo->USU_recibe)->USU_nombre;	

		if ($flujo->USU_recibe == 4) {
			$motivo .= " -- juego de facturación --";
		}

		$titulo = "El Usuario " . $usuariorecibe . " rechazó la entrega";
		$descripcion = "Se rechazó el documento por el area de " . $arearecibe . " por el usuario: " . $usuariorecibe . " mandado por " . $usuarioentrega . " del area de " . $areaentrega . " por motivo: "  . $motivo;

		$historial = new Historico;
		$historial->HIS_folio = $folio;
		$historial->HIS_fecha =  date('d/m/Y H:i:s'); 
		$historial->HIS_hora =  date('d/m/Y H:i:s'); 
		$historial->HIS_titulo =  $titulo; 
		$historial->HIS_descripcion =  $descripcion; 
		$historial->HIS_area =  $flujo->ARO_porRecibir;
		$historial->HIS_usuario =  $flujo->USU_recibe;
		$historial->HIS_accion = 'Rechazo';
		$historial->HIS_etapa =  $etapa;
		$historial->HIS_entrega =  $entrega;
		$historial->DOC_claveint =  $documento;  
		$historial->FLD_claveint =  $clave;
		$historial->save();
		
		return  true;

    }

}
