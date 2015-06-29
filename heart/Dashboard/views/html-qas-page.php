<div class="">
 	<h2><?php echo _e("Q & A"); ?></h2>
	<p>Some questions and answers are present  here , also welcome to go to <a target="blank" href="http://colorvila.com/qdiscuss">our qdiscuss forum.</a></p>
	<p>If you have some install problems, please go to <a href="http://colorvila.com/qdiscuss" target='blank'>our official QDiscuss forum</a> to start a discussion under Feedback category.</p>
	<hr>
	<div class="container">
		<div class="panel ">
			<div class="panel-heading">Which PHP version support QDiscuss</div>
			<div class="panel-body">
	        			PHP version  must be 5.4 or higher.
	    		</div>
		</div>
		<div class="panel ">
			<div class="panel-heading">QDiscuss can operate in mobile browser</div>
			<div class="panel-body">
	        			Yes! QDiscuss is full responsive base on Bootstrap, you can view, edit on mobile phone.
	    		</div>
		</div>
		<div class="panel ">
			<div class="panel-heading">How to install extensions</div>
			<div class="panel-body">
	        			<ul>
	        				<li>
	        					Online Install and Upgrade:
	        					<p>Just go to <a href="<php echo admin_url('admin.phppage=qdiscuss-extensions'); >">Extensions Page</a>, click the Browser All Extensions, and click install.</p>
	        				</li>
	        				<li>
	        					Manually Install:
	        					<p>Download extension from  <a  target="blank" href="http://colorvila.com/qdiscuss-extensions/">official extensions gallery</a>, then unzip the extension, and move it into the <strong>wp-content/qdiscuss/extensions</strong> directory. Don't forget activate the extension at <a href="<php echo admin_url('admin.phppage=qdiscuss-extensions'); >">Extensions Page</a></p>
	        				</li>
	        			</ul>
	    		</div>
		</div>
	</div>
	<?php include('html-footer.php'); ?>
</div>
