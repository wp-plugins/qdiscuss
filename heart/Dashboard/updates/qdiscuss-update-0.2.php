<?php

use Illuminate\Database\Schema\Blueprint;
use Qdiscuss\Core\Support\Schema;
use Qdiscuss\Core\Models\Setting;

if (!Schema::hasColumn('discussions', 'view_counts')) {
	Schema::table('discussions', function(Blueprint $table){
		$table->integer('view_counts')->unsigned()->default(0);
	});
}

if (!Setting::getValueByKey('forum_language')) {
	Setting::insert(
		[
			'key' => 'forum_language',
			'value' => 'en',
		]
	);
}

// Setting::setValue('extensions_enabled', '[]');

if (!Schema::hasTable('attachments')) {
	Schema::create('attachments', function(Blueprint $table){
		$table->increments('id');
		$table->integer('user_id')->unsigned();
		$table->string('path');
		$table->timestamps();
	});
}

