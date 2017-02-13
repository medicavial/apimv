<?php

/*
|
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
|
*/

set_time_limit(3600);
ini_set('memory_limit', '-1');


Route::group(array('prefix' => 'api'), function()
{
	Route::get('/', function()
	{
	    $anio = date('Y'); 
	    $mes = date('m'); 

		$fechaIni  = '2016-12-01 21:59:00';
		$ultimaFechaFac = date('Y-m-d', strtotime( $fechaIni ));
        $ultimaHoraFac =  date('H:i', strtotime( '+1 minutes' , strtotime($fechaIni) ) );
        $fechaActual = date('Y-m-d');

        if ( $fechaActual == $ultimaFechaFac) {
          $fechaFactura = date('Y-m-d') . " " . $ultimaHoraFac;
        }else{
          $fechaFactura = date('Y-m-d') . ' 21:00';
        }

        return $fechaFactura;


	});

	Route::get('/monitor', function(){
		return DB::select("EXEC MVMonitorCaptura");
	});

	Route::get('/multiasistencia', function(){
		return DB::select("EXEC MV_MON_Multiasistencia");
	});

    Route::post('login', array('uses' => 'UserController@login'));
    Route::get('logout', array('uses' => 'UserController@logout'));

	//movimientos dentro del flujo para el alta de un nuevo folio registrado
    Route::post('altaoriginal', array('uses' => 'FlujoController@altaoriginal'));
    //movimiento que se utiliza para actualizar de factrua express a original
    Route::post('actualizaoriginal', array('uses' => 'FlujoController@actualizaoriginal'));
    //movimiento que cambia un bit para que sea reconocido como folio para "no pagar hasta cobrar"
    Route::post('insertanpc', array('uses' => 'FlujoController@insertanpc'));
    Route::post('eliminanpc', array('uses' => 'FlujoController@eliminanpc'));
    Route::post('posiblenp', array('uses' => 'FlujoController@posiblenp'));

    //busquedas de control de documentos
	Route::group(array('prefix' => 'controldocumentos'), function()
	{
    	Route::post('folio', array('uses' => 'ControlController@folio'));
    	Route::post('lesionado', array('uses' => 'ControlController@lesionado'));
    	Route::post('fechas', array('uses' => 'ControlController@fechas'));
	});

	//busquedas de tickets normales y de pagos
	Route::group(array('prefix' => 'tickets'), function()
	{
		Route::post('', array('uses' => 'TicketController@altaticket'));
		Route::put('', array('uses' => 'TicketController@actualizaticket'));
    	Route::post('consulta', array('uses' => 'TicketController@consulta'));
    	Route::get('categorias', array('uses' => 'TicketController@categorias'));
    	Route::get('detalle/{foliointerno}/{folio}', array('uses' => 'TicketController@detalle'));
    	Route::get('folio/{folio}', array('uses' => 'TicketController@folio'));
    	Route::get('foliointerno/{folio}', array('uses' => 'TicketController@foliointerno'));
    	Route::get('maximo', array('uses' => 'TicketController@maximo'));
    	Route::get('status', array('uses' => 'TicketController@status'));
    	Route::get('subcategorias/{categoria}', array('uses' => 'TicketController@subcategorias'));

    	Route::group(array('prefix' => 'pagos'), function()
		{
			Route::post('', array('uses' => 'TicketController@altaticketpagos'));
			Route::put('', array('uses' => 'TicketController@actualizaticketpagos'));
	    	Route::post('consulta', array('uses' => 'TicketController@consultapagos'));
	    	Route::get('categorias', array('uses' => 'TicketController@categoriaspagos'));
	    	Route::get('detalle/{foliointerno}/{folio}', array('uses' => 'TicketController@detallepagos'));
	    	Route::get('folio/{folio}', array('uses' => 'TicketController@foliopagos'));
	    	Route::get('foliointerno/{folio}', array('uses' => 'TicketController@foliointernopagos'));
	    	Route::get('status', array('uses' => 'TicketController@statuspagos'));
	    	Route::get('subcategorias/{categoria}', array('uses' => 'TicketController@subcategoriaspagos'));
		});

	});

	Route::group(array('prefix' => 'consulta'), function()
	{
		Route::get('areas', array('uses' => 'BusquedaController@areas'));
		Route::get('ajustadores/{cliente}', array('uses' => 'BusquedaController@ajustador'));
		Route::get('buscador/{consulta}/{tipo}', array('uses' => 'BusquedaController@buscador'));
		Route::get('concepto/{tipo}', array('uses' => 'BusquedaController@concepto'));
		Route::get('editaDatos/{folio}', array('uses' => 'BusquedaController@editaDatos'));
		Route::get('empresas', array('uses' => 'BusquedaController@empresas'));
		Route::get('empresasweb', array('uses' => 'BusquedaController@empresasweb'));
	    Route::get('escolaridad', array('uses' => 'BusquedaController@escolaridad'));
	    Route::get('folio/{folio}', array('uses' => 'BusquedaController@folio'));
	    Route::get('folioweb/{folio}', array('uses' => 'BusquedaController@folioweb'));
	    Route::get('flujo/{folio}', array('uses' => 'BusquedaController@flujo'));
	    Route::get('historial/{folio}/{etapa}/{entrega}', array('uses' => 'BusquedaController@historial'));
	    Route::get('lesionado/{lesionado}', array('uses' => 'BusquedaController@lesionado'));
	    Route::get('medicos/{unidad}', array('uses' => 'BusquedaController@medicos'));
	    Route::get('posiciones', array('uses' => 'BusquedaController@posiciones'));
	    Route::get('productos', array('uses' => 'BusquedaController@productos'));
	    Route::get('productos/{empresa}', array('uses' => 'BusquedaController@productosEmp'));
	    Route::get('referencia/{unidad}', array('uses' => 'BusquedaController@referencia'));
	    Route::get('riesgos', array('uses' => 'BusquedaController@riesgos'));
	    Route::get('subsecuencia/{folio}/{entrega}', array('uses' => 'BusquedaController@subsecuencia'));
	    Route::get('solicitudes', array('uses' => 'BusquedaController@solicitudes'));
	    Route::get('rehabilitacion/{folio}/{entrega}', array('uses' => 'BusquedaController@rehabilitacion'));
	    Route::get('tipoLesion/{tipo}', array('uses' => 'BusquedaController@tipoLesion'));
	    Route::get('triage', array('uses' => 'BusquedaController@triage'));
	    Route::get('usuarios', array('uses' => 'BusquedaController@usuarios'));
	    Route::get('usuarios/{area}', array('uses' => 'BusquedaController@usuariosarea'));
	    Route::get('usuariostodos/{area}', array('uses' => 'BusquedaController@usuariostodosarea'));
	    Route::get('usuariosweb', array('uses' => 'BusquedaController@usuariosweb'));
	    Route::get('unidades', array('uses' => 'BusquedaController@unidades'));
	    Route::get('unidades/red', array('uses' => 'BusquedaController@unidadesRed'));
	    Route::get('unidadesweb', array('uses' => 'BusquedaController@unidadesweb'));
	    Route::get('lesiones/{tipoLes}', 'BusquedaController@lesiones');
	    Route::get('lesionweb', 'BusquedaController@lesionWeb');
	    Route::get('tipotramite', 'BusquedaController@tipotramite');
	    Route::get('verificaetapaentrega/{folio}/{etapa}', array('uses' => 'BusquedaController@verificaetapaentrega'));
	    Route::get('verificafolio/{folio}/{etapa}', array('uses' => 'BusquedaController@verificafolio'));
	    Route::get('verificafoliopase/{folio}', array('uses' => 'BusquedaController@verificafoliopase'));
	    Route::get('verificaprefijo/{prefijo}/{empresa}', array('uses' => 'BusquedaController@verificaprefijo'));
	    Route::get('impuestos', array('uses' => 'BusquedaController@impuestos'));
	    Route::get('impuestovalor/{id}', array('uses' => 'BusquedaController@impuestovalor'));
	    Route::get('unidadesref/{clave}', array('uses' => 'BusquedaController@unidadesref'));

	    
	});

	Route::group(array('prefix' => 'entregas'), function()
	{
    	Route::post('acepta', array('uses' => 'EntregasController@acepta'));
    	Route::post('rechaza', array('uses' => 'EntregasController@rechaza'));
    	
	});


    Route::group(array('prefix' => 'flujo'), function()
	{
		Route::get('consulta/{usuario}', array('uses' => 'FlujoController@consulta'));
		Route::post('alta', array('uses' => 'FlujoController@alta'));
		Route::get('altaManualJF', array('uses' => 'FlujoController@altaManualJF'));
		Route::post('actualiza', array('uses' => 'FlujoController@actualiza'));
		Route::get('activos/{usuario}', array('uses' => 'FlujoController@activos'));
		Route::post('elimina', array('uses' => 'FlujoController@elimina'));
		Route::get('entregas/{usuario}', array('uses' => 'FlujoController@entregas'));
		Route::get('recepcion/{usuario}', array('uses' => 'FlujoController@recepcion'));
		Route::get('rechazos/{usuario}', array('uses' => 'FlujoController@rechazos'));
		Route::get('npc/{usuario}', array('uses' => 'FlujoController@npc'));

		Route::get('activosarea/{area}', array('uses' => 'FlujoController@activosarea'));
		Route::get('entregasarea/{area}', array('uses' => 'FlujoController@entregasarea'));
		Route::get('recepcionarea/{area}', array('uses' => 'FlujoController@recepcionarea'));
		Route::get('rechazosarea/{area}', array('uses' => 'FlujoController@rechazosarea'));

	});

	Route::group(array('prefix' => 'flujopagos'), function()
	{

		Route::get('general', array('uses' => 'FlujopagosController@general'));
		Route::post('fecharecepcion', array('uses' => 'FlujopagosController@fecharecepcion'));
		Route::post('fechapagos', array('uses' => 'FlujopagosController@fechapagos'));

	});

	Route::group(array('prefix' => 'operacion'), function()
	{
		Route::post('editaDatos', array('uses' => 'OperacionController@editaDatos'));
		Route::get('estatusUnidad/{unidad}/{bit}/{usuario}', array('uses' => 'OperacionController@estatusUnidad'));
		Route::get('expedienteInfo/{folio}', array('uses' => 'OperacionController@expedienteInfo'));
		Route::get('generaFactura/{folio}', array('uses' => 'OperacionController@generaFactura'));
		Route::get('guardaImagenes/{folio}', array('uses' => 'OperacionController@guardaImagenes'));
		Route::post('guardaTramite', array('uses' => 'OperacionController@guardaTramite'));
		Route::post('usuarios', array('uses' => 'OperacionController@guardaUsuario'));
		Route::put('usuarios/{id}', array('uses' => 'OperacionController@editaUsuario'));
		Route::get('riesgo/{folio}/{valor}', array('uses' => 'OperacionController@editaRiesgo'));
		Route::get('pdfImagen/{archivo}', array('uses' => 'OperacionController@pdfImagen'));

	});

	Route::group(array('prefix' => 'qualitas'), function()
	{
		Route::post('actualiza/{envio}', array('uses' => 'QualitasController@actualiza'));
		Route::get('consulta/{folio}', array('uses' => 'QualitasController@consulta'));
    	Route::post('envios', array('uses' => 'QualitasController@envios'));
		Route::get('envios/{envio}', array('uses' => 'QualitasController@envio'));
    	Route::post('genera', array('uses' => 'QualitasController@generaArchivos'));
    	Route::post('general', array('uses' => 'QualitasController@general'));
    	Route::post('principal', array('uses' => 'QualitasController@principal'));
    	Route::get('procesado/{envio}', array('uses' => 'QualitasController@procesado'));
    	Route::post('procesa', array('uses' => 'QualitasController@procesa'));
    	Route::post('rechazos', array('uses' => 'QualitasController@rechazos'));
    	Route::get('reporte/{reporte}', array('uses' => 'QualitasController@reporte'));
    	Route::post('renombrar', array('uses' => 'QualitasController@renombrar'));
    	Route::get('renombrarIndividual/{folio}', array('uses' => 'QualitasController@renombrarIndividual'));
    	Route::post('sinarchivo', array('uses' => 'QualitasController@sinarchivo'));
    	Route::post('sinprocesar', array('uses' => 'QualitasController@sinprocesar'));
    	Route::post('sinprocesarFE', array('uses' => 'QualitasController@sinprocesarFE'));
    	Route::get('test/{folio}', array('uses' => 'QualitasController@test'));
    	Route::post('incompletos', array('uses' => 'QualitasController@incompletos'));
    	Route::post('invalidos', array('uses' => 'QualitasController@invalidos'));
	});


	Route::group(array('prefix' => 'qualitasFE'), function()
	{
		
		Route::post('actualiza/{envio}', array('uses' => 'QualitasFEController@actualiza'));
    	Route::post('general', array('uses' => 'QualitasFEController@general'));
    	Route::post('incompletos', array('uses' => 'QualitasFEController@incompletos'));
    	Route::post('invalidos', array('uses' => 'QualitasFEController@invalidos'));
    	Route::post('principal', array('uses' => 'QualitasFEController@principal'));
    	Route::post('rechazos', array('uses' => 'QualitasFEController@rechazos'));
    	Route::post('sinarchivo', array('uses' => 'QualitasFEController@sinarchivo'));
    	Route::post('sinprocesar', array('uses' => 'QualitasFEController@sinprocesar'));

	});

	Route::group(array('prefix' => 'reportes'), function()
	{
    	Route::post('controldocumentos', array('uses' => 'ReportesController@control'));
    	Route::get('descargar/{archivo}', array('uses' => 'ReportesController@descargar'))->where('archivo', '[A-Za-z0-9\-\_\.]+');
    	Route::post('facturas', array('uses' => 'ReportesController@facturas'));
    	Route::post('pago/unidades', array('uses' => 'ReportesController@pagoUnidad'));
    	Route::get('tickets', array('uses' => 'ReportesController@tickets'));
    	Route::get('ticketsdia', array('uses' => 'ReportesController@ticketsdia'));
    	Route::post('ticketsdia', array('uses' => 'ReportesController@ticketsdiaespecifico'));
	});


	Route::group(array('prefix' => 'facturacionExpress'), function()
	{

    	Route::post('autorizados', array('uses' => 'FacturacionExpressController@autorizados'));
    	Route::put('actualizaFolio', array('uses' => 'FacturacionExpressController@actualizaFolio'));
    	Route::get('consultaCedula/{cedula}/{nombre}', array('uses' => 'FacturacionExpressController@consultaCedula'));
    	Route::post('captura', array('uses' => 'FacturacionExpressController@captura'));
    	Route::post('capturaCuestionario', array('uses' => 'FacturacionExpressController@capturaCuestionario'));
    	Route::post('capturaAjustador', array('uses' => 'FacturacionExpressController@capturaAjustador'));
		Route::get('detalleFolio/{folio}', array('uses' => 'FacturacionExpressController@detalleFolio'));
    	Route::post('pendientes', array('uses' => 'FacturacionExpressController@pendientes'));
    	Route::post('rechazados', array('uses' => 'FacturacionExpressController@rechazados'));
    	Route::post('solicitarAutorizacion', array('uses' => 'FacturacionExpressController@solicitarAutorizacion'));
    	Route::post('solicitarAutorizacionRechazos', array('uses' => 'FacturacionExpressController@solicitarAutorizacionRechazos'));
    	Route::post('solicitados', array('uses' => 'FacturacionExpressController@solicitados'));
    	Route::get('cartas/{fecha}', array('uses' => 'FacturacionExpressController@cartas'));

	});

	Route::group(array('prefix' => 'facturacionExpressBeta'), function()
	{
    	Route::post('autorizados', array('uses' => 'FacturacionExpressBetaController@autorizados'));
    	Route::put('actualizaFolio', array('uses' => 'FacturacionExpressBetaController@actualizaFolio'));
    	Route::post('captura', array('uses' => 'FacturacionExpressBetaController@captura'));
    	Route::post('capturaCuestionario', array('uses' => 'FacturacionExpressBetaController@capturaCuestionario'));
    	Route::post('capturaAjustador', array('uses' => 'FacturacionExpressBetaController@capturaAjustador'));
		Route::get('detalleFolio/{folio}', array('uses' => 'FacturacionExpressBetaController@detalleFolio'));
    	Route::post('pendientes', array('uses' => 'FacturacionExpressBetaController@pendientes'));
    	Route::post('rechazados', array('uses' => 'FacturacionExpressBetaController@rechazados'));
    	Route::post('solicitarAutorizacion', array('uses' => 'FacturacionExpressBetaController@solicitarAutorizacion'));
    	Route::post('solicitarAutorizacionRechazos', array('uses' => 'FacturacionExpressController@solicitarAutorizacionRechazos'));
    	Route::post('solicitados', array('uses' => 'FacturacionExpressBetaController@solicitados'));
	});


	Route::group(array('prefix' => 'saceco'), function(){
		
    	Route::post('autorizados', array('uses' => 'SacecoController@autorizados'));
    	Route::put('actualizaFolio', array('uses' => 'SacecoController@actualizaFolio'));
    	Route::post('doctosValidados', array('uses' => 'SacecoController@doctosValidados'));
    	Route::post('captura', array('uses' => 'SacecoController@captura'));
    	Route::post('capturaCuestionario', array('uses' => 'SacecoController@capturaCuestionario'));
    	Route::post('capturaAjustador', array('uses' => 'SacecoController@capturaAjustador'));
		Route::get('detalleFolio/{folio}/{atencion}', array('uses' => 'SacecoController@detalleFolio'));
    	Route::post('pendientes', array('uses' => 'SacecoController@pendientes'));
    	Route::post('rechazados', array('uses' => 'SacecoController@rechazados'));
    	Route::post('solicitarAutorizacion', array('uses' => 'SacecoController@solicitarAutorizacion'));
    	Route::post('solicitarAutorizacionRechazos', array('uses' => 'SacecoController@solicitarAutorizacionRechazos'));
    	Route::post('solicitados', array('uses' => 'SacecoController@solicitados'));
    	Route::post('autorizadosSinCaptura', array('uses' => 'SacecoController@autorizadosSinCaptura'));
	});


    Route::group(array('prefix' => 'RelacionPagos'), function()
	{
        // Route::post('fechaEntrega', array('uses' => 'RelacionController@relacionFechaEnt'));
        Route::post('buscaxProveedor/{id}', array('uses' => 'RelacionController@buscaxProveedor'));
        Route::post('insertaRelacion/{usuario}', array('uses' => 'RelacionController@insertaRelacion'));
        Route::post('insertaRelacionGlo/{usuario}', array('uses' => 'RelacionController@insertaRelacionGlo'));
        Route::post('upload/{usuario}', array('uses' => 'RelacionController@upload'));
        Route::post('eliminaxml', array('uses' => 'RelacionController@eliminaxml'));
        Route::post('eliminaxmlInd/{idx}', array('uses' => 'RelacionController@eliminaxmlInd'));
        Route::get('borratemporales/{usuario}', array('uses' => 'RelacionController@borratemporales'));
        Route::post('buscaRelacion', array('uses' => 'RelacionController@busquedaRelaciones'));
        Route::post('consultaFolioFiscal/{foliofiscal}', array('uses' => 'RelacionController@consultaFolioFiscal'));
        Route::post('borrarxArchivo/{foliofiscal}/{usuario}', array('uses' => 'RelacionController@borrarxArchivo'));
        Route::post('validaUnidad/{rfc}', array('uses' => 'RelacionController@validaUnidad'));

    });

    Route::group(array('prefix' => 'FacturaUnidades'), function()
	{
        Route::post('buscaFolios', array('uses' => 'FacturaUnidadesController@buscaFolios'));
        Route::post('enviaFolios/{usuario}', array('uses' => 'FacturaUnidadesController@enviaFolios'));
        Route::post('actualiza', array('uses' => 'FacturaUnidadesController@actualiza'));
        Route::post('listadoFactura', array('uses' => 'FacturaUnidadesController@listadoFactura'));
        Route::post('buscaxUnidad/{id}', array('uses' => 'FacturaUnidadesController@buscaxUnidad'));
        Route::post('unidades', array('uses' => 'FacturaUnidadesController@unidades'));
        Route::post('ordenPago', array('uses' => 'FacturaUnidadesController@ordenPago'));
        Route::post('borratemporales', array('uses' => 'FacturaUnidadesController@borratemporales'));
        Route::post('listadofacturas', array('uses' => 'FacturaUnidadesController@listadofacturas'));
        Route::post('listadofacturasxfecha', array('uses' => 'FacturaUnidadesController@listadofacturasxfecha'));



    });

    Route::group(array('prefix' => 'DetalleFacturas'), function()
	{
        Route::post('imprimeDatos/{folio}', array('uses' => 'DetalleFacturasController@imprimeDatos'));
        Route::post('rechazaFactura', array('uses' => 'DetalleFacturasController@rechazaFactura'));
        Route::post('revisa', array('uses' => 'DetalleFacturasController@revision'));


    });	

    Route::group(array('prefix' => 'FacturaZima'), function()
	{
        Route::post('unidades', array('uses' => 'FacturaZimaController@unidades'));

    });	

    Route::group(array('prefix' => 'RelacionNP'), function()
	{
        Route::post('fechaRegistro', array('uses' => 'RelacionNoPagadaController@fechaRegistro'));
        Route::post('generaReporte', array('uses' => 'RelacionNoPagadaController@generaReporte'));
        
    });

    Route::group(array('prefix' => 'OrdenesPago'), function()
	{
        Route::post('listadoOrdenPago', array('uses' => 'OrdenesPagoController@listadoOrdenPago'));
        Route::post('aceptaOrden', array('uses' => 'OrdenesPagoController@aceptaOrden'));

    });

    Route::group(array('prefix' => 'DetalleRelacion'), function()
	{
        Route::post('listadetalleRelacion/{relacion}', array('uses' => 'DetalleRelacionController@listadetalleRelacion'));
        Route::post('generaReporte', array('uses' => 'DetalleRelacionController@generaReporte'));

    });





		Route::get('/xml', function(){

			// $app = new Illuminate\Container\Container;
			// $document = new Orchestra\Parser\Xml\Document($app);
			// $reader = new Orchestra\Parser\Xml\Reader($document);
			$xml = $reader->load('/archivo.xml');
			// $user = $xml->parse([
			//     'id' => ['uses' => 'user.id'],
			//     'email' => ['uses' => 'user.email'],
			//     'followers' => ['uses' => 'user::followers'],
			// ]);
			return $xml;
			print_r($xml);

	    });

	});

Route::get('/', array('uses' => 'HomeController@index'));
Route::get('/procesos', array('uses' => 'HomeController@procesos'));
Route::get('/et1', array('uses' => 'HomeController@subeEt1'));
Route::get('/et2', array('uses' => 'HomeController@subeEt2'));
Route::get('/et3', array('uses' => 'HomeController@subeEt3'));

Route::get('/et1Especial', array('uses' => 'HomeController@subeEt1Especial'));
Route::get('/et2Especial', array('uses' => 'HomeController@subeEt2Especial'));
Route::get('/et3Especial', array('uses' => 'HomeController@subeEt3Especial'));

Route::get('/especial', array('uses' => 'HomeController@subePagoCobreEspecial'));

Route::get('/documento', array('uses' => 'HomeController@SubeDocumento'));

Route::get('/info', function()
{
	phpinfo();
});



