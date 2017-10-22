<?php

class Mail {
	function Mail() {
		$mail = new PHPMailer;

		// configure these to match your mail server settings
		$mail->isSMTP();
		$mail->Host = '';
		$mail->SMTPAuth = true;
		$mail->Username = '';
		$mail->Password = '';
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;

		$this->mail = $mail;
	}

	function send($to, $from, $subject, $msg) {
		$mail = $this->mail;

		$mail->setFrom('connor@efight.me', $from);
		$mail->addAddress($to);

		$mail->isHTML(true);

		$mail->Subject = $subject;
		$mail->Body = $msg;

		return $mail->send();
	}
}
