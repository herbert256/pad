<?php

  // The fixture of helpers: the member put into the session, and the variable with it.

  padSessionPut ( 'member', 'ann' );

  $helpersPut = "put $member " . padSession ( 'member' );

?>
