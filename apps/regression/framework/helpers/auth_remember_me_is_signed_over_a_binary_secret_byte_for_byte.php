<?php

  // The remember-me cookie is signed over the row's secret fields, so that renewing one - a
  // remember_token column - ends every cookie made before it. A token that is no UTF-8 text,
  // the raw bytes of a BINARY column, was signed as null: two different tokens signed alike,
  // and renewing it ended nothing.

  $one = padAuthSecrets ( [ 'id' => 1, 'remember_token' => "\xff\x01abc", 'password' => '$2y$12$abc' ] );
  $two = padAuthSecrets ( [ 'id' => 1, 'remember_token' => "\xfe\x02xyz", 'password' => '$2y$12$abc' ] );

  $signed = ( $one === $two ) ? 'alike' : 'apart';

?>
