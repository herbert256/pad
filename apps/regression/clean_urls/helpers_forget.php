<?php

  // The fixture of helpers: the member taken out of the session, and out of the variable.

  padSessionForget ( 'member' );

  $helpersForget = 'forgot [' . $member . '] ' . ( padSessionHas ( 'member' ) ? 'still there' : 'gone' );

?>
