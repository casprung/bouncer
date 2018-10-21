<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html>
<head>
  <!-- Standard Meta -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

  <!-- Site Properties -->
  <title>Performing Logon</title>
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/reset.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/site.css">

  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/container.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/grid.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/header.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/image.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/menu.css">

  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/divider.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/segment.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/form.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/input.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/button.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/list.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/message.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/icon.css">
  <link rel="stylesheet" type="text/css" href="/assets/semantic/components/loader.css">

  <style type="text/css">
    body {
      background-color: #DADADA;
    }
    body > .grid {
      height: 100%;
    }
    .image {
      margin-top: -100px;
    }
    .column {
      max-width: 450px;
    }
  </style>
</head>
<body onload="document.APLoginForm.submit()">
	<div class="ui middle aligned center aligned grid">
		<div class="column">
			<div class="ui active massive indeterminate text loader">Logon in progress</div>
			<form name="APLoginForm" method="post" action="http://208.132.55.21/reg.php">
				<input name="username" type="hidden" value="<?php echo $username; ?>">
				<input name="challenge" type="hidden" value="<?php echo $challenge; ?>">
				<input name="password" type="hidden" value="<?php echo $response; ?>">
				<input name="url" type="hidden" value="<?php echo $url; ?>">
			</form>
		</div>
	</div>
</body>
</html>
