<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['useragent']   = 'CodeIgniter';
$config['protocol']    = 'smtp';
$config['smtp_host']   = getenv('SMTP_HOST') ? getenv('SMTP_HOST') : "localhost";
$config['smtp_port']   = getenv('SMTP_PORT') ? getenv('SMTP_PORT') : 25;
$config['smtp_user']   = getenv('SMTP_USER') ? getenv('SMTP_USER') : "sender@localhost";
$config['smtp_pass']   = getenv('SMTP_PASS') ? getenv('SMTP_PASS') : "secret";
$config['smtp_crypto'] = getenv('SMTP_CRYPTO') ? getenv('SMTP_CRYPTO') : "";
$config['smtp_timeout'] = 30;
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['wordwrap'] = TRUE;
