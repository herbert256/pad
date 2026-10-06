<?php

  // Flashes two messages and renders itself, showing the error kind only - the fixture of
  // request/flash_typed.

  padFlash ( 'Saved.', 'success' );
  padFlash ( 'Look again.', 'error' );

?>
