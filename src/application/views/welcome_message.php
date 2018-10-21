<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">
        <style type="text/css">
            body {
                background-color:#3686be;
                color:#FFF;
                border:0px;
                margin:0px;
                overflow:hidden;
            }
            .wrapper {
                position:absolute;
                top:15%;
                left:0px;
                right:0px;
                width:100%;
                height:auto;
            }
            .sammy {
                float:left;
                width:100%;
                height:auto;
                text-align:center;
            }
            .message {
                float:left;
                width:100%;
                height:auto;
                font-size:36px;
                text-align:center;
                font-family:Arial,Helvetica,sans-serif;
                margin-top:25px;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <div class="sammy"><img src="https://assets.digitalocean.com/other_images/sammy.png"/></div>
            <div class="message">Please log into your droplet via SSH to configure your LAMP installation.</div>
        </div>
    </body>
</html>
