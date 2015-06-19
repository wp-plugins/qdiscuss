<?php

use Illuminate\Database\Schema\Blueprint;
use Qdiscuss\Core\Support\Schema;

if (!Schema::hasColumn('users', 'display_name')) {
	Schema::table('users', function(Blueprint $table){
		$table->string('display_name', 100)->after('username');
	});
}
