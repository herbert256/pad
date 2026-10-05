<?php

  // A request that brings the padFlash cookie takes the flash messages the request before
  // it left in the session (lib/flash.php) - once, a restart keeps what was taken.

  if ( ! padSecondTime ( 'flashTake' ) )
    padFlashTake ();

?>
