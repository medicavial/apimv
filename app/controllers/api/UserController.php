<?php

class UserController extends BaseController {

	public function login(){

		// $user = Input::get('user');
		// $psw = Input::get('psw');
		// if (Auth::attempt(array('USU_login' => $user, 'USU_password' => $psw ))){
		// 	return Auth::user();
		// }else{
		// 	return Response::json(array('respuesta' => 'Nombre de Usuario o contraseña Invalida'), 500); 
		// }

		$user = Input::get('user');
		$psw = md5(Input::get('psw'));
		$usuario = DB::table('Usuario')
					->leftJoin('UsuarioArea', 'Usuario.USU_claveint', '=', 'UsuarioArea.USU_claveint')
					->select('Usuario.USU_claveint as clave', 'USU_Nombre as nombre', 'ARO_claveint as area', 'USU_login as usuario', 'USU_usuarioWeb as usuarioweb')
					->where('USU_login','=',$user)
					->where('USU_password','=',$psw)
					->take(1)
					->get();

		if ($usuario) {

			return $usuario;

		}else{

			return Response::json(array('respuesta' => 'Nombre de Usuario o contraseña Invalida'), 500); 
		}

	}

	public function logout(){

		Auth::logout();
		return Response::json(array('respuesta' => 'Sesion cerrada exitosamente'));
	}
}
