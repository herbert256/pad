<?php

  // A card's security code and number under the names the payment forms give them, the
  // one-time codes under their common names, a PIN, and the password word of other
  // languages stay out of the session - the name field alone comes back.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'card_code' => 'a', 'cardCode' => 'b', 'csc' => 'c', 'cvn' => 'd', 'card_number' => 'e', 'cardNumber' => 'f',
             'cc_number' => 'g', 'one_time_code' => 'h', 'oneTimeCode' => 'i', 'sms_code' => 'j', 'twofa_code' => 'k',
             '2fa_code' => 'l', 'mfa_code' => 'm', 'verification_code' => 'n', 'pin' => 'o', 'pin_code' => 'p',
             'wachtwoord' => 'q', 'passwort' => 'r', 'kennwort' => 's', 'mot_de_passe' => 't', 'motdepasse' => 'u',
             'contrasena' => 'v', 'contraseña' => 'w', 'senha' => 'x', 'parola' => 'y', 'haslo' => 'z', 'hasło' => 'A',
             'salasana' => 'B', 'losenord' => 'C', 'lösenord' => 'D', 'name' => 'ann' ];

  padFlashInput ();

  $flashed = implode ( ' ', array_intersect ( array_keys ( $_POST ), array_keys ( padOld () ) ) );

?>
