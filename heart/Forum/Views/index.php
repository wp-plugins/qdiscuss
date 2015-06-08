<!doctype html>
<html>
  <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo  $config['forum_title'] ; ?></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
	<?php foreach ($styles as $file) : 
		$css_path = str_replace(base_path(), '', $file);?>
		<link rel="stylesheet" href="<?php echo  plugins_url($css_path[0],  __DIR__.'/../../../../')  ;?>">
	<?php endforeach; ?>
  </head>

  <body>
	<?php include("layout.php"); ?>
	<div id="modal"></div>
	<div id="alerts"></div>

	<?php foreach ($scripts as $file) : 
		$js_path = str_replace(base_path(), '', $file); ?>
		<script src="<?php echo  plugins_url($js_path[0],  __DIR__.'/../../../../')  ;?>"></script>
	<?php endforeach; ?>
	<script>
		var app = require('flarum/app')['default'];
		app.config = <?php echo json_encode($config); ?>;
		app.language = <?php echo $language !="" ? $language : "{}"; ?>;
		app.preload = {
		data: <?php echo json_encode($data); ?>,
		session: <?php echo  json_encode($session); ?>
		};
		app.boot();
	</script>
  </body>
</html>


