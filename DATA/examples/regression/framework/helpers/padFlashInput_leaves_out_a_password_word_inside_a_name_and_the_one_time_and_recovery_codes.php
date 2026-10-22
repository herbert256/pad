<?php

  // Django's password1 and password2, newpassword, WordPress's user_pass, Drupal's pass, an
  // installer's db_pass, the one-time and recovery codes of a second factor, the answer to a
  // security question and the keys that are credentials stay out of the session - the name
  // field alone comes back.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'password1' => 'a', 'password2' => 'b', 'newpassword' => 'c', 'oldPassword' => 'd', 'pass' => 'e',
             'user_pass' => 'f', 'db_pass' => 'g', 'pwd2' => 'h', 'passphrase2' => 'i', 'otp' => '1', 'otp_code' => '2',
             'totp' => '3', 'backup_code' => '4', 'recovery_code' => '5', 'reset_code' => '6', 'verification_code' => '7',
             'security_answer' => 'j', 'secret_answer' => 'k', 'client_secret' => 'l', 'api_key' => 'm', 'apiKey' => 'n',
             'private_key' => 'o', 'secret_key' => 'p', 'license_key' => 'q', 'access_token' => 'r', 'name' => 'ann' ];

  padFlashInput ();

  $flashed = implode ( ' ', array_intersect ( array_keys ( $_POST ), array_keys ( padOld () ) ) );

?>
