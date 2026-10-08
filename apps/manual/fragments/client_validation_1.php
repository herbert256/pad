<?php

  // The server checks every post against the same rules - the browser's check only saves
  // the visitor a round trip.

  if ( padPosted ( 'signup' ) ) {

    padFlash ( 'Welcome aboard.' );

    padRedirect ();

  }

?>
