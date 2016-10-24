<?php

class UserController extends BaseController{

	public function login(){

		$user = Input::get('user');
		$psw = md5(Input::get('psw'));
		$usuario = User::leftJoin('UsuarioArea', 'Usuario.USU_claveint', '=', 'UsuarioArea.USU_claveint')
					->select('Usuario.USU_claveint as clave', 'USU_Nombre as nombre', 'ARO_claveint as area', 'USU_login as usuario', 'USU_usuarioWeb as usuarioweb','Usuario.*')
					->where('USU_login','=',$user)
					->where('USU_password','=',$psw)
					->get(0);

		$usuarioCuenta = User::where('USU_login','=',$user)
					->where('USU_password','=',$psw)
					->count();

		if ($usuarioCuenta > 0) {

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
