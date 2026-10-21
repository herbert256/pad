<?php

  // What {debug}, the toolbar's variables and the reports show of a user row: a password
  // hash under the names a users table gives it - password_hash, passwordHash, pass_hash -
  // is redacted as the password is, where only a name ending in the word counted and the
  // hash showed in clear. A name that only looks like one stays.

  $hashUser = [ 'name' => 'Ann', 'password' => 'p1', 'password_hash' => 'h1', 'passwordHash' => 'h2',
                'pass_hash' => 'h3', 'passcode' => 'c1', 'passenger' => 'Bob' ];

  $hashShown = [];

  foreach ( padRedact ( $hashUser ) as $hashKey => $hashValue )
    $hashShown [] = "$hashKey=" . ( $hashValue === '*** redacted ***' ? 'redacted' : $hashValue );

  $hashShown = implode ( ' ', $hashShown );

?>
