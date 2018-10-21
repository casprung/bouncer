<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div class="ui middle aligned center aligned grid">
	<div class="column">
		<h2 class="ui teal image header">
			<img src="/assets/images/wifi.png" class="image">
			<div class="content">
				Request network access
			</div>
		</h2>
		<?php echo form_open($this->router->fetch_class().'/register', 'class="ui large form" id="guestRegisterForm" onSubmit="document.getElementById(\'submit\').disabled=true; document.getElementById(\'submit\').innerText = \'Please Wait\';"'); ?>
			<div class="ui segment">
				<h4 class="ui dividing header">Personal Identification</h4>
				<div class="field">
					<label>Full Name</label>
					<div class="field">
						<div class="ui left icon input">
							<i class="address card icon"></i>
							<input type="text" name="name" placeholder="Your Name" maxlength="255" value="<?php echo set_value('name'); ?>">
						</div>
					</div>
				</div>
				<div class="field">
					<label>Contact Information</label>
					<div class="field">
						<div class="ui left icon input">
							<i class="mail icon"></i>
							<input type="email" name="email" placeholder="Your e-mail address" maxlength="254" value="<?php echo set_value('email'); ?>">
						</div>
					</div>
					<div class="field">
						<div class="ui left icon input">
							<i class="mobile icon"></i>
							<input type="tel" name="mobile" placeholder="Mobile phone number" maxlength="16" value="<?php echo set_value('mobile'); ?>">
						</div>
					</div>
				</div>
				<h4 class="ui dividing header">Sponsor Information</h4>
				<div class="field">
					<div class="ui left icon input">
						<i class="mail outline icon"></i>
						<input type="email" name="sponsor" placeholder="Sponsor e-mail address" maxlength="254" value="<?php echo set_value('sponsor'); ?>">
					</div>
				</div>
				<button class="ui fluid large teal labeled icon button" id="submit" type="submit">Submit<i class="large edit right icon"></i></button>
			</div>
			<?php echo validation_errors('<div class="ui message">', '</div>'); ?>
		</form>
	</div>
</div>
