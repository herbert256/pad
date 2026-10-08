<?php

  // Writes a line to the application's log - what pad work and pad schedule are checked by
  // on the command line.

  padLog ( 'stamp {what}', 'info', [ 'what' => $what ?? 'scheduled' ] );

?>
