<?php

  // padLogin keeps a row's secrets out of the session by more names - a hash, a pass code,
  // a pin, an api key - and any value PHP knows as a password hash: in a column named hash
  // or pass_hash it went into the session, {auth} and the dumps, and was not signed into
  // the remember-me cookie, so a new password left every cookie valid.

  padLogin ( [ 'id' => 7, 'name' => 'Eve', 'hash' => 'x0', 'pass_hash' => 'x1', 'passcode' => '1234', 'pin' => '0000',
               'password' => 'p', 'api_key' => 'k', 'credential' => padHash ( 'z' ), 'pinned' => 'yes' ] );

  $kept = implode ( ',', array_keys ( $_SESSION ['padAuth'] [ padAuthApp () ] ['user'] ) );

  padLogout ();

?>
