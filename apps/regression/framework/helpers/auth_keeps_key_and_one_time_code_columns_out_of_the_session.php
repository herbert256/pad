<?php

  // A row's key and one-time code columns are secrets too: Yii's auth_key - renewed to end
  // remembered logins - reset_code, login_code, verification_code, activation_key and the
  // like went into the session, {auth} and the dumps. A code or a key of no login - a
  // country's, a postal one - stays an ordinary field.

  padLogin ( [ 'id' => 7, 'username' => 'eve', 'auth_key' => 'kT3x9Qm2', 'verification_code' => '493817',
               'reset_code' => '772210', 'loginCode' => '118833', 'activation_key' => 'f00d', 'magic_link' => 'https://x/?t=1',
               'session_key' => 's', 'security_answer' => 'Rex', 'resetcode' => '1', 'reset_code_sent_at' => '2026-10-21',
               'country_code' => 'NL', 'postal_code' => '1234 AB', 'access_level' => 'admin' ] );

  $kept = implode ( ',', array_keys ( $_SESSION ['padAuth'] [ padAuthApp () ] ['user'] ) );

  padLogout ();

?>
