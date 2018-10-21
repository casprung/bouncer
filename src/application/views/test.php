<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div class="ui middle aligned center aligned grid">
	<div class="left aligned column">
		<h2 class="ui top attached header">
			<i class="dashboard icon"></i>
			<div class="content">
				Device Limit Reached
				<div class="sub header">Concurrent active devices are limited</div>
			</div>
		</h2>
		<div class="ui attached segment">
			<p>You're trying to activate more devices than allowed by network policy. If you want to continue an existing device will be disconnected. You can choose which one with the switches below.</p>
		</div>
		<div class="ui attached segment">
			<form class="ui large form">
				<div class="field">
					<div class="ui slider checkbox">
						<input name="mac" checked="checked" type="radio">
						<label>F0-9F-C2-0C-AB-81</label>
					</div>
				</div>
				<div class="field">
					<div class="ui slider checkbox">
						<input name="mac" type="radio">
						<label>D8-D4-3C-F4-89-E4</label>
					</div>
				</div>
				<div class="field">
					<div class="ui slider checkbox">
						<input name="mac" type="radio">
						<label>38-3F-10-01-BA-BC</label>
					</div>
				</div>
				<div class="field">
					<div class="ui slider checkbox checked">
						<input name="mac" type="radio">
						<label>88-5B-DD-0A-11-C0</label>
					</div>
				</div>
				<button class="ui fluid large teal labeled icon button" type="submit">Continue<i class="large chevron circle right icon"></i></button>
			</form>
		</div>
	</div>
</div>
