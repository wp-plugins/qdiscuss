<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $config['forumTitle'] . ' Dashboard'; ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="<?php echo $config['modulePrefix']; ?>/config/environment" content="<?php echo  rawurlencode(json_encode($config)); ?>">
    <link rel="stylesheet" href="<?php echo $css_url; ?>">
  </head>
<body>
<div id="assets-loading" class="fade">Loading...</div>
   <script>
       setTimeout(function() {
           var loading = document.getElementById('assets-loading');
           if (loading) {
               loading.className += ' in';
           }
       }, 1000);
   </script>

<script>
        var QDISCUSS_DATA = <?php echo $data;?>;
        var QDISCUSS_SESSION = <?php echo $session;?>;
        var QDISCUSS_ALERT = null;
    </script>
    <script src="<?php echo $js_url; ?>"></script>   
  </body>
</html>
