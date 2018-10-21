<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div class="ui middle aligned center aligned grid">
	<div class="left aligned column">
		<h2 class="ui top attached header">
			<i class="dashboard icon"></i>
			<div class="content">
				<?php echo $header; ?>
				<div class="sub header"><?php echo $message; ?></div>
			</div>
		</h2>
		<div class="ui attached segment">
			<p>
				You're trying to activate more devices than allowed by the network policy. 
				If you continue the existing device will be disconnected. 
				If there is more then one active device you can choose the device with the switches below.
			</p>
		</div>
		<div class="ui attached segment">
			<?php echo form_open($this->router->fetch_class().'/endpoints', 'class="ui large form" id="guestEndpointForm"');
				$suchmuster = '/([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i';
				$checked=1;
				foreach ($endpoints as $endpoint) {
					echo '<div class="field">'."\n";
					echo '	<div class="ui slider checkbox">'."\n";
					// if only a single endpoint is active then show checked and disabled selector
					if (count($endpoints) == 1) {
						echo '		<input name="mac" checked="checked" disabled="disabled" type="radio" value="'.$endpoint['mac'].'">'."\n";
					} elseif ($checked==1) {
						$checked++;
						echo '		<input name="mac" checked="checked" type="radio" value="'.$endpoint['mac'].'">'."\n";
					} else {
						echo '		<input name="mac" type="radio" value="'.$endpoint['mac'].'">'."\n";
					}
					echo '		<label>'.preg_replace($suchmuster, '$1-$2-$3-$4-$5-$6', strtoupper($endpoint['mac'])).'</label>'."\n";
					echo '	</div>'."\n";
					echo '</div>'."\n";
				}
			?>
				<button class="ui fluid large teal labeled icon button" type="submit">Continue<i class="large chevron circle right icon"></i></button>
			</form>
		</div>
	</div>
</div>