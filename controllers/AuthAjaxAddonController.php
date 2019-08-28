<?php

namespace Modules\AuthAjaxAddon\Controllers;

use Kernel\Request;
use Modules\AuthAjaxAddon\Models\Tokens;

class AuthAjaxAddonController extends \Extensions\Controller{
	/**
	 * Установка модуля
	 *
	 * @method install
	 */
	public function install(){
		module('AuthAjaxAddon') -> install();
	}

	/**
	 * Контроллер регистрации
	 *
	 * @method signup
	 *
	 * @return [string] Результаты операции в json формате
	 */
	public function signup(){
		Request::clear();
		$post = Request::post();
		$result = Tokens::ins() -> signup($post);
		return json_encode([
			'status' => $result ? 'fail' : 'success',
			'errortxt' => $result,
			'post_mirror' => $post
		]);
	}

	/**
	 * Контроллер входа в систему
	 *
	 * @method signin
	 *
	 * @return [string] Результаты операции в json формате
	 */
	public function signin(){
		Request::clear();
		$post = Request::post();
		$result = Tokens::ins() -> signin($post);

		return json_encode([
			'status' => !is_array($result['result']) ? 'fail' : 'success',
			'errortxt' => !is_array($result['result']) ? $result['result'] : NULL,
			'post_mirror' => $post,
			'ip' => is_array($result['result']) ? $result['ip'] : NULL,
			'token' => is_array($result['result']) ? $result['token'] : NULL,
		]);
	}

	/**
	 * Контроллер выхода из системы
	 *
	 * @method signout
	 *
	 * @param  [string] $token Строка токена
	 *
	 * @return [string] Результаты операции в json формате
	 */
	public function signout($token){
		$result = Tokens::ins() -> signout($token);

		return json_encode([
			'status' => !$result ? 'fail' : 'success',
			'token' => $token,
		]);
	}

	public function get_user_data($token){
		$user = Tokens::ins() -> get_user_by_token($token);
		
	}
}