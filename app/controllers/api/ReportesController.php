<?php

class ReportesController extends BaseController {

	public function control(){

		$folios =  Input::all(); 

		$datos = Excel::create('Listado', function($excel) use($folios) {

				    $excel->sheet('Reporte', function($sheet) use($folios) {

				        $sheet->fromArray($folios);

				    });

				})->store('xls', public_path('exports'),true);

		return $datos;
	}

	public function descargar($archivo){

		$ruta = public_path() .'/exports/'. $archivo;


	    if (File::exists($ruta))
	    {
	        return Response::download($ruta);
	    }
	    else
	    {
	        exit('El archivo no existe en el servidor!');
	    }

	}

	public function facturas(){

		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 

		return DB::select("EXEC MV_LIS_FacturaXRecepcion  @fechaIni='$fechaini', @fechaFin='$fechafin 23:59:58.999'");
	}

	public function pagoUnidad(){

		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin');

		return DB::select(" EXEC MV_PAG_Listado  @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999' ");
	}

	

	public function tickets(){

		$fechaini = '2015-05-01 00:00:00';
		$fechafin = date('Y-m-d') . ' 23:59:59';

		$datos = array();

		$ticketsClinica = Tickets::join('Expediente', 'Expediente.Exp_folio', '=', 'TicketSeguimiento.Exp_folio')
				->join('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->select(DB::raw('COUNT(Tseg_clave) as Cantidad , UNI_nombreMV as Unidad'))
				->where('Uni_propia','S')
				->where('TCat_clave',1)
				->where('TStatus_clave','<>',7)
				->whereBetween('TSeg_fechareg', array($fechaini, $fechafin))
				->groupBy('Expediente.Uni_clave')
				->get();

		$ticketsCat = Tickets::join('Expediente', 'Expediente.Exp_folio', '=', 'TicketSeguimiento.Exp_folio')
				->join('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->join('TicketSubcat', 'TicketSubcat.TSub_clave', '=', 'TicketSeguimiento.TSub_clave')
				->select(DB::raw('COUNT(TicketSeguimiento.Tseg_clave) as Cantidad, TSub_nombre as Categoria'))
				->where('Uni_propia','S')
				->where('TicketSeguimiento.TCat_clave',1)
				->where('TStatus_clave','<>',7)
				->whereBetween('TSeg_fechareg', array($fechaini, $fechafin))
				->groupBy('TicketSeguimiento.TSub_clave')
				->get();

		$datos['clinicas'] = $ticketsClinica;
		$datos['categorias'] = $ticketsCat;

		return $datos;

	}


	public function ticketsdia(){

		$fechaini = date('Y-m-d') . ' 00:00:00';
		$fechafin = date('Y-m-d') . ' 23:59:59';

		$datos = array();

		$ticketsClinica = Tickets::join('Expediente', 'Expediente.Exp_folio', '=', 'TicketSeguimiento.Exp_folio')
				->join('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->select(DB::raw('COUNT(Tseg_clave) as Cantidad , UNI_nombreMV as Unidad'))
				->where('Uni_propia','S')
				->where('TCat_clave',1)
				->where('TStatus_clave','<>',7)
				->whereBetween('TSeg_fechareg', array($fechaini, $fechafin))
				->groupBy('Expediente.Uni_clave')
				->get();

		$ticketsCat = Tickets::join('Expediente', 'Expediente.Exp_folio', '=', 'TicketSeguimiento.Exp_folio')
				->join('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->join('TicketSubcat', 'TicketSubcat.TSub_clave', '=', 'TicketSeguimiento.TSub_clave')
				->select(DB::raw('COUNT(TicketSeguimiento.Tseg_clave) as Cantidad, TSub_nombre as Categoria'))
				->where('Uni_propia','S')
				->where('TicketSeguimiento.TCat_clave',1)
				->where('TStatus_clave','<>',7)
				->whereBetween('TSeg_fechareg', array($fechaini, $fechafin))
				->groupBy('TicketSeguimiento.TSub_clave')
				->get();

		$datos['clinicas'] = $ticketsClinica;
		$datos['categorias'] = $ticketsCat;

		return $datos;

	}


	public function ticketsdiaespecifico(){

		$fecha = Input::get('fecha');


		// $fechaNueva = date('Y-m-d',strtotime($fecha));
		$fechaNueva = DateTime::createFromFormat('d/m/Y', $fecha);
		$otra = $fechaNueva->format('Y-m-d');

		$fechaini = $otra . ' 00:00:00';
		$fechafin = $otra . ' 23:59:59';

		$datos = array();

		$ticketsClinica = Tickets::join('Expediente', 'Expediente.Exp_folio', '=', 'TicketSeguimiento.Exp_folio')
				->join('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->select(DB::raw('COUNT(Tseg_clave) as Cantidad , UNI_nombreMV as Unidad'))
				->where('Uni_propia','S')
				->where('TCat_clave',1)
				->where('TStatus_clave','<>',7)
				->whereBetween('TSeg_fechareg', array($fechaini, $fechafin))
				->groupBy('Expediente.Uni_clave')
				->get();

		$ticketsCat = Tickets::join('Expediente', 'Expediente.Exp_folio', '=', 'TicketSeguimiento.Exp_folio')
				->join('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->join('TicketSubcat', 'TicketSubcat.TSub_clave', '=', 'TicketSeguimiento.TSub_clave')
				->select(DB::raw('COUNT(TicketSeguimiento.Tseg_clave) as Cantidad, TSub_nombre as Categoria'))
				->where('Uni_propia','S')
				->where('TicketSeguimiento.TCat_clave',1)
				->where('TStatus_clave','<>',7)
				->whereBetween('TSeg_fechareg', array($fechaini, $fechafin))
				->groupBy('TicketSeguimiento.TSub_clave')
				->get();

		$datos['clinicas'] = $ticketsClinica;
		$datos['categorias'] = $ticketsCat;

		return $datos;

	}


}
