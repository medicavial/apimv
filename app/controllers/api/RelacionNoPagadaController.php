<?php

class RelacionNoPagadaController extends BaseController {

	public function fechaRegistro(){

	    DB::disableQueryLog();
		  $fechaini =  Input::get('fechainiReg'); 
	    $fechafin =  Input::get('fechafinReg'). ' 23:59:58.999'; 


		  $relacionado =DB::table('RelacionFiscal')
                  ->select(DB::raw('RelacionFiscal.REL_clave as relacion,
                                    CASE WHEN REL_global = 0 THEN \'Individual\' ELSE \'Global\' END as tiporelacion, 
                                    CASE WHEN REL_completa = 0 THEN \'Incompleta\' ELSE \'Completa\' END as completa,
                                    CASE WHEN REL_aplicada = 0 THEN \'Noaplicada\' ELSE \'Aplicada\' END as aplicada,
                                    RELF_fcreada as creada, USU_nombre as nombre'))
                  ->join('RelacionFechas', 'RelacionFechas.REL_clave', '=', 'RelacionFiscal.REL_clave')
                  ->join('RelacionUsuarios', 'RelacionUsuarios.REL_clave', '=', 'RelacionFiscal.REL_clave')
                  ->join('Usuario', 'RelacionUsuarios.USU_creo', '=', 'Usuario.USU_claveint')
                  ->get();

        return $relacionado;
	}



}
