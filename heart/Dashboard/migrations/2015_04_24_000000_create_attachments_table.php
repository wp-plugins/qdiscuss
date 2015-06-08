<?php

use Illuminate\Database\Schema\Blueprint;
use Qdiscuss\Core\Support\Schema;

if (!Schema::hasTable('attachments')) {
	Schema::table('attachments', function(Blueprint $table){
		$table->increments('id');
		$table->integer('user_id')->unsigned();
		$table->string('path');
		$table->timestamps();
	});
}
