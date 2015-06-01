<?php

class HomeController extends BaseController {

	public function index()
	{
		$datos = DB::select("SELECT EMP_NombreCorto as cliente,
						PRO_nombre as Producto,
						TRI_nombre as Triage,
						PAS_fechaCaptura as FechaCaptura,
						Pase.PAS_folio as Folio,
						REP_fechaConsulta as FechaAtencion,
						AFE_sexo as Sexo,
						AFE_fechaNac as FechaNacimiento,
						AFE_cp as CodigoPostal,
						UPPER(DATENAME(MONTH ,PAS_fechaCaptura)) as mes,
						RIGHT('00' +   CAST(DAY(PAS_fechaCaptura) as varchar) ,2) as dia,
						LOWER(Pase.PAS_folio) as foliol,
						'id_' + LOWER(Pase.PAS_folio) + '.jpg' as imagen
				FROM Pase INNER JOIN Documento  ON Pase.PAS_folio          = DOC_folio AND DOC_etapa = 1         
						  INNER JOIN Empresa    ON Empresa.EMP_claveint    = Pase.EMP_claveint
						  INNER JOIN Etapa1     ON Etapa1.PAS_folio        = Pase.PAS_folio
						  INNER JOIN Expediente ON Expediente.ET1_claveint = Etapa1.ET1_claveint
						  INNER JOIN Reporte    ON Reporte.REP_claveint    = Etapa1.REP_claveint
						  INNER JOIN Medico     ON Medico.MED_claveint     = Reporte.MED_tratante
						  INNER JOIN Unidad     ON Unidad.UNI_claveint     = Medico.UNI_claveint
						  INNER JOIN Localidad  ON Localidad.LOC_claveint  = Unidad.LOC_claveint
						  INNER JOIN DatosSiniestro ON DatosSiniestro.DAS_claveint = Pase.DAS_claveint
						  INNER JOIN Triage ON Triage.TRI_claveint = Pase.TRI_claveint
						  INNER JOIN Producto ON Producto.PRO_claveint = Documento.PRO_claveint
						  INNER JOIN Afectado ON Afectado.AFE_claveint = PAse.AFE_claveint
						  LEFT JOIN ExpedienteWeb ON EXW_folio = Pase.PAS_folio
				WHERE PAS_cancelado = 0 AND Unidad.UNI_claveint = 56 and PAS_fechaCaptura between '01/01/2015' and '30/04/2015 23:59:58.999'");
			
			// $data[] = array();


			foreach ($datos as $dato){

				$cliente = $dato->cliente;
				$ano = '2015';
				$mes = $dato->mes;
				$dia = $dato->dia;
				$folio = $dato->foliol;
				$archivo = $dato->imagen;
				$ruta = "\\\\MVIMG\\Respaldo\\SOPORTES\\". $cliente . "\\" . $ano . "\\". $mes ."\\" . $dia .' ' . $mes . "\\" . $folio . "\\" . $archivo;
				$ruta2 = "\\\\172.17.10.10\\Respaldo\\SOPORTES\\". $cliente . "\\" . $ano . "\\". $mes ."\\" . $dia .' ' . $mes . "\\" . $folio . "\\" . $archivo;
			   	
			    if(File::exists($ruta)){

				    $data[] = array(
				        "cliente"=> $dato->cliente,
				        "producto" => $dato->Producto,
				        "triage"=> $dato->Triage,
				        "FechaCaptura"=> $dato->FechaCaptura,
				        "Folio"=> $dato->Folio,
				        "FechaAtencion"=> $dato->FechaAtencion,
				        "CP"=> $dato->CodigoPostal,
				        "Imagen" => $ruta2
				    );

			    }
			    
			}


		// return $data;

		Excel::create('AtencionesRoma', function($excel) use($data) {

		    $excel->sheet('datos', function($sheet) use($data) {

		        $sheet->fromArray($data);

		    });

		})->export('xls');


	}

}
