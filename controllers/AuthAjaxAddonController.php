<?php

namespace Modules\AuthAjaxAddon\Controllers;

use Kernel\Request;
use Modules\AuthAjaxAddon\Models\Tokens;

class AuthAjaxAddonController extends \Extensions\Controller{
	public function install(){
		module('AuthAjaxAddon') -> install();
	}

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

	public function signout($token){
		$result = Tokens::ins() -> signout($token);

		return json_encode([
			'status' => !$result ? 'fail' : 'success',
			'token' => $token,
		]);
	}
}