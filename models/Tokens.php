<?php

namespace Modules\AuthAjaxAddon\Models;

use \Modules\Auth\Models\Users;

class Tokens extends \Extensions\Model{

	public $table = "Tokens";

	public function default_rows(){
		return [];
	}

	public function signin($data){
		$result = module('Auth') -> signin($data);
		if(!is_array($result)){
			$result = module('Auth') -> get_err_by_errcode($result);
		}else{
			$user_id = current_signin_id();
			$token = $this -> token_generate($user_id);
			$ip = $this -> get_ip();
			$token_id = $this -> set(compact('user_id', 'token', 'ip'));
		}

		return [
			'result' => $result,
			'token' => is_array($result) ? $token : NULL,
			'ip' => is_array($result) ? $ip : NULL,
		];
	}

	public function signout($token){
		module('Auth') -> signout();
		return $this -> one() -> token($token) -> remove();
	}

	public function signup($data){
		$result = module('Auth') -> signup($data);
		if($result){
			$result = module('Auth') -> get_err_by_errcode($result);
		}
		return $result;
	}

	private function token_generate($user_id){
		$token = sha1($user_id . time() . rand(0, 1000));
		return $token;
	}

	private function get_ip(){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	public function get_by_token($token){
		return $this -> one() -> token($token);
	}

}
