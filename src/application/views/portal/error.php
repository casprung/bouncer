<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div class="ui middle aligned center aligned grid">
	<div class="left aligned column">
		<h2 class="ui top attached header">
			<i class="warning sign icon"></i>
			<div class="content">
				Bad Request
				<div class="sub header"><?php echo $message; ?></div>
			</div>
		</h2>
		<div class="ui attached segment">
			<ul class="list">
				<?php echo validation_errors('<li>', '</li>'); ?>
			</ul>
		</div>
	</div>
</div>
