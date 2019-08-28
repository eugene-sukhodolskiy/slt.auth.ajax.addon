<?php

/* /migrations/ */
use Kernel\DBW;

class TokensMigration extends \Extensions\Migration{

	public function up(){
		// Create tables in db
		DBW::create('Tokens',function($t){
			$t -> int('user_id')
			-> varchar('token', 255)
			-> varchar('ip', 50)
			-> timestamp('date_of_update')
			-> timestamp('date_of_create');
		});

		return true;
	}

	public function down(){
		// Drop tables from db
		DBW::drop('Tokens');

		return true;
	}

}

