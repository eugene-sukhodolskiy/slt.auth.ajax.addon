<?php

function auth_ajax_addon_routes_map(){
	route('\\Modules\\AuthAjaxAddon\\Controllers\\AuthAjaxAddonController@install');
	route('auth-signup', '\\Modules\\AuthAjaxAddon\\Controllers\\AuthAjaxAddonController@signup', '/auth-ajax-addon/signup');
	route('auth-signin', '\\Modules\\AuthAjaxAddon\\Controllers\\AuthAjaxAddonController@signin', '/auth-ajax-addon/signin');
}