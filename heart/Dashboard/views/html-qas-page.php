<?php include('html-header.php'); ?>

<div class="wrap  qd-wrap">
 	<h3>Q & A</h3>
 	<hr>
	<p>Some questions and answers are present  here , also welcome to go to <a target="blank" href="http://colorvila.com/qdiscuss">our qdiscuss forum</a></p>
	<h4>1. Which PHP version support QDiscuss?</h4>
	<p>PHP version  must be 5.4 or higher.</p>
	<h4>2. QDiscuss can operate in mobile browser?</h4>
	<p>Yes! QDiscuss is full responsive base on Bootstrap, you can view, edit on mobile phone.</p>
	<h4>3. How to install extensions?</h4>
	<ul>
		<li>
			<strong>Online Install and Upgrade:</strong>
			<p>Just go to <a href="<?php echo admin_url('admin.php?page=qdiscuss-extensions'); ?>">Extensions Page</a>, click the Browser All Extensions, and click install.</p>
		</li>
		<li>
			<strong>Manually Install:</strong>
			<p>Download extension from  <a  target="blank" href="http://colorvila.com/qdiscuss-extensions/">official extensions gallery</a>, then unzip the extension, and move it into the <strong>wp-content/qdiscuss/extensions</strong> directory. Don't forget activate the extension at <a href="<?php echo admin_url('admin.php?page=qdiscuss-extensions'); ?>">Extensions Page</a></p>
		</li>
	</ul>
	
	<p>If you have some install problems, please go to <a href="http://colorvila.com/qdiscuss" target='blank'>our official QDiscuss forum</a> to start a discussion under Feedback category.</p>
	<?php include('html-footer.php'); ?>
</div>
