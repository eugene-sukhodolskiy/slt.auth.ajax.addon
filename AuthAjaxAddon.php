<?php

namespace Modules\AuthAjaxAddon;
use Kernel\Maker\Maker;
use Kernel\Module;

class AuthAjaxAddon{
	/**
	 * Хранит путь к директории данного модуля
	 *
	 * @var [string]
	 */
	public $p2m;

	public $module_name = "AuthAjaxAddon";

	/**
	 * Срок жизни токена в сукундах
	 *
	 * @var [int]
	 */
	public $token_life = 60*60*24;

	public function __construct(){
		$this -> p2m = Module::pathToModule($this -> module_name);
		include_once($this -> p2m . "routes.map.php");
		auth_ajax_addon_routes_map();
	}

	/**
	 * Установка всего необходимого для работы модуля
	 *
	 * @method install
	 *
	 */
	public function install(){
		if(Maker::migration_up("Tokens", $this -> p2m . 'migrations', false)){
			echo "<h4>AuthAjaxAddon Installation Success</h4>";
		}else{
			echo "<h4>AuthAjaxAddon Installation Error!</h4>";
		}
	}
}