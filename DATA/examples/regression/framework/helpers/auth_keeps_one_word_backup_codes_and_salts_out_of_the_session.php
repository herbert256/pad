<?php

  // padLogin keeps the one-word spellings of backup codes, salts and pin numbers out of the
  // session as it does their split ones - backupcodes, otpbackupcodes, usersalt, pwdsalt,
  // pinnumber beside backup_codes, user_salt and pin_number: they were kept, in padUser,
  // {auth}{debug}{/auth} and every dump, and backup codes log a user in.

  padLogin ( [ 'id' => 7, 'name' => 'Asha', 'backupcodes' => 'AAAA-BBBB,CCCC-DDDD', 'otpbackupcodes' => 'EEEE-FFFF',
               'usersalt' => 'pepper123', 'pwdsalt' => 'pepper456', 'pinnumber' => '4321',
               'backup_codes' => 'GGGG-HHHH', 'user_salt' => 'pepper789', 'pin_number' => '8765',
               'backup_email' => 'asha@home.example' ] );

  $kept = implode ( ',', array_keys ( padUser () ) );

  padLogout ();

?>
