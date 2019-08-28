<?php

namespace Modules\AuthAjaxAddon\Models;

use \Modules\Auth\Models\Users;

class Tokens extends \Extensions\Model{

	/**
	 * Для хранения закэшированных данных
	 *
	 * @var array
	 */
	private $cache = [];

	public $table = "Tokens";

	public function default_rows(){
		return [
			'status' => '1'
		];
	}

	/**
	 * Метод входа в систему
	 *
	 * @method signin
	 *
	 * @param  [array] $data Данные для входа, настраиваются в модуле auth
	 *
	 * @return [array] Данные результата входа
	 */
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

	/**
	 * Выход из системы
	 *
	 * @method signout
	 *
	 * @param  [string] $token Токен который нужно подрезать
	 *
	 * @return [boolean] Результат выхода из системы
	 */
	public function signout($token){
		module('Auth') -> signout();
		return $this -> die_token($token);
	}

	/**
	 * Метод для регистрации в системе
	 *
	 * @method signup
	 *
	 * @param  [array] $data Данные для регистрации. Минимально необходимый набор данных указывается в настройках модуля auth
	 *
	 * @return [boolean or int] Результаты регистрации, false при успешной регистрации или int код ошибки. Коды ошибок прописаны в модуле auth
	 */
	public function signup($data){
		$result = module('Auth') -> signup($data);
		if($result){
			$result = module('Auth') -> get_err_by_errcode($result);
		}
		return $result;
	}

	/**
	 * Служебный метод для генерации строкового токена
	 *
	 * @method token_generate
	 *
	 * @param  [int] $user_id Идентификатор пользователя
	 *
	 * @return [string] Сгенерированный токен
	 */
	private function token_generate($user_id){
		$token = sha1($user_id . time() . rand(0, 1000));
		return $token;
	}

	/**
	 * Служебный метод для определения ip пользователя
	 *
	 * @method get_ip
	 *
	 * @return [string] строка с ip пользователя
	 */
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

	/**
	 * Метод для получения данные связанные с токеном
	 *
	 * @method get_by_token
	 *
	 * @param  [string] $token токен соединения
	 *
	 * @return [object] объект с данными о токене
	 */
	public function get_by_token($token){
		return $this -> cache_code("get_by_token:{$token}", function() use ($token){
			return $this -> one() -> token($token);
		});
	}

	/**
	 * Метод для получения пользователя по токену
	 *
	 * @method get_user_by_token
	 *
	 * @param  [string] $token Строка токена
	 *
	 * @return [object] объект пользователя
	 */
	public function get_user_by_token($token){
		return Users::ins() -> one() -> id($this -> get_user_id_by_token($token));
	}

	/**
	 * Метод для получения id пользователя по токену
	 *
	 * @method get_user_id_by_token
	 *
	 * @param  [string] $token Строка токена
	 *
	 * @return [int] Идентификатор пользователя
	 */
	public function get_user_id_by_token($token){
		return $this -> get_by_token($token) -> user_id;
	}

	/**
	 * Служебный метод для сессионного кэширования данных
	 *
	 * @method cache_code
	 *
	 * @param  [string] $name уникальное название
	 * @param  [function] $code код в виде анонимной функции, результат выполнения которого необходимо закэшировать
	 *
	 * @return [data] Результаты выполнения кода, который кэшируется
	 */
	private function cache_code($name, $code){
		$this -> cache[$name] = isset($this -> cache[$name]) ? $this -> cache[$name] : $code();
		return $this -> cache[$name];
	}

	/**
	 * Проверка токена на актуальность
	 *
	 * @method is_live
	 *
	 * @param  [string] $token Строка токена
	 *
	 * @return [boolean] true or false
	 */
	public function is_live($token){
		return $this -> get_by_token($token) -> status ? true : false;
	}

	/**
	 * Метод для убийства токена
	 *
	 * @method die_token
	 *
	 * @param  [string] $token_string Строка токена
	 *
	 * @return [boolean] Результат выполнения
	 */
	public function die_token($token_string){
		$token = $this -> get_by_token($token_string);
		$token -> status = '0';
		return $token -> update();
	}

}
