<?php

  // A request that brings the padFlash cookie takes the flash messages the request before
  // it left in the session (lib/flash.php) - once, a restart keeps what was taken. The
  // padOld cookie does the same for the input padFlashInput kept (lib/request.php).

  if ( ! padSecondTime ( 'flashTake' ) )
    padFlashTake ();

  if ( ! padSecondTime ( 'oldTake' ) )
    padOldTake ();

?>
