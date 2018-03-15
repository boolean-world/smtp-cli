#!/usr/bin/env php
<?php
require_once __DIR__.'/vendor/autoload.php';

set_error_handler(function($errno, $errstr) {
	throw new RuntimeException($errstr);
});

$config = json_decode(file_get_contents(__DIR__.'/config.json'), true);

$subject = $argv[1] ?? null;
if ($subject === null) {
	echo "No subject!\n";
	exit(1);
}

$receipients = array_slice($argv, 2);
if (!$receipients) {
	echo "No receipients set!\n";
	exit(1);
}

$msg = '';
$stdin = fopen('php://stdin', 'r');
while (($line = fgets($stdin, 256)) !== false) {
	$msg .= $line;
}

$mail = new Nette\Mail\Message;
$mail->setFrom($config['username']);
foreach ($receipients as $receipient) {
	$mail->addTo($receipient);
}
$mail->setSubject($subject);
$mail->setBody($msg);

$mailer = new Nette\Mail\SmtpMailer($config);
$mailer->send($mail);
