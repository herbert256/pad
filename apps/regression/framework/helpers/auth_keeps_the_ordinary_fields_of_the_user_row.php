<?php

  // padLogin keeps the user row without its secrets - a name that is a secret's as a whole
  // word, or a value that is a password hash - and with its ordinary fields: email_hash,
  // hashtag, otp_enabled, recovery_email and pin_code_sent were dropped as well.

  padLogin ( [ 'id' => 7, 'name' => 'Ann', 'password' => 'x', 'otp_secret' => 's', 'pin' => '1234', 'recovery_codes' => 'r', 'api_key' => 'k',
               'hash' => password_hash ( 'pw', PASSWORD_BCRYPT, [ 'cost' => 4 ] ),
               'email_hash' => 'b58996c504c5638798eb6b511e6f49af', 'otp_enabled' => 1, 'recovery_email' => 'ann@home.example',
               'hashtag' => '#pad', 'pin_code_sent' => 0 ] );

  $kept = implode ( ',', array_keys ( padUser () ) );

  padLogout ();

?>
