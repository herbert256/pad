<?php

  // padLogin keeps the user row without its secrets - a name holding a secret's word, unless
  // its last word marks a flag, a time, a count or an address, or a value that is a password
  // hash - and with its ordinary fields: email_hash, hashtag, otp_enabled, recovery_email and
  // pin_code_sent were dropped as well, and then recovery_key, otp_backup_codes, pin_hash,
  // remember_hash and otp_code were kept, and then pincode, totpseed, pwdhash, userpwd.

  padLogin ( [ 'id' => 7, 'name' => 'Ann', 'password' => 'x', 'otp_secret' => 's', 'pin' => '1234', 'recovery_codes' => 'r', 'api_key' => 'k',
               'recovery_key' => 'rk', 'otp_backup_codes' => 'b', 'pin_hash' => hash ( 'sha256', '1234' ), 'remember_hash' => 'h', 'otp_code' => '123456',
               'pincode' => '1234', 'recoverycodes' => 'r', 'totpseed' => 's', 'pwdhash' => 'h', 'userpwd' => 'p', 'PINCode' => '1234',
               'hash' => password_hash ( 'pw', PASSWORD_BCRYPT, [ 'cost' => 4 ] ),
               'email_hash' => 'b58996c504c5638798eb6b511e6f49af', 'otp_enabled' => 1, 'recovery_email' => 'ann@home.example',
               'hashtag' => '#pad', 'pin_code_sent' => 0 ] );

  $kept = implode ( ',', array_keys ( padUser () ) );

  padLogout ();

?>
