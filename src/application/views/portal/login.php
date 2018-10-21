<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div class="ui middle aligned center aligned grid">
	<div class="column">
		<h2 class="ui teal image header">
			<img src="/assets/images/wifi.png" class="image">
			<div class="content">
				Log-in to our network
			</div>
		</h2>
		<?php
			//$hidden = array('url' => $url, 'ssid' => $ssid, 'mac' => $mac, 'challenge' => $challenge);
			//echo form_open($this->router->fetch_class().'/login', 'class="ui large form" id="guestLoginForm" onSubmit="document.getElementById(\'submit\').disabled=true; document.getElementById(\'submit\').innerText = \'Please Wait\';"', $hidden);
			echo form_open($this->router->fetch_class().'/login', 'class="ui large form" id="guestLoginForm" onSubmit="document.getElementById(\'submit\').disabled=true; document.getElementById(\'submit\').innerText = \'Please Wait\';"');
		?>
			<div class="ui segment">
				<div class="field">
					<div class="ui left icon input">
						<i class="user icon"></i>
						<input type="email" name="email" placeholder="E-mail address">
					</div>
				</div>
				<div class="field">
					<div class="ui left icon input">
						<i class="lock icon"></i>
						<input type="password" name="password" placeholder="Password">
					</div>
				</div>
				<button class="ui fluid large teal labeled icon button" id="submit" type="submit">Login<i class="large sign in right icon"></i></button>
			</div>
			<?php echo validation_errors('<div class="ui message">', '</div>'); ?>
		</form>
		<div class="ui message">
			Need an Account? <?php echo anchor($this->router->fetch_class().'/register', 'Register'); ?>
		</div>
	</div>
</div>
