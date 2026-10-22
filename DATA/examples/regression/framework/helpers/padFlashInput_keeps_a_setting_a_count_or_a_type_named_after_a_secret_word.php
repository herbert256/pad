<?php

  // A credential word with a word that makes it a setting, a count or a type of it - otp_enabled,
  // enable_otp, otp_method, pass_count, pass_type, secrets_count - names no secret, nor does
  // a token word that is no token (max_tokens, tokens, a tokenizer), a secret_santa, or the
  // code of a conference session or an app: they come back after padFlashInput (); padBack ().

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'max_tokens' => '256', 'tokenizer' => 'bpe', 'tokens' => '12', 'otp_enabled' => '1', 'enable_otp' => '1',
             'otp_method' => 'sms', 'secrets_count' => '3', 'secret_santa' => 'Bob', 'session_code' => 'S12',
             'app_code' => 'crm', 'pass_count' => '2', 'pass_type' => 'day', 'cid' => 'C42' ];

  padFlashInput ();

  $flashed = [];

  foreach ( array_keys ( $_POST ) as $flashKey )
    $flashed [] = "$flashKey=[" . padOld ( $flashKey ) . ']';

  $flashed = implode ( ' ', $flashed );

?>
