<?php

  // Fires from level/start_end/start1.php when a tag body is about to be split on '@start@',
  // and records use of the 'start' construct in the xref report.

  global $padInfoXref;

  if ( $padInfoXref  )
    padInfoXref ( 'constructs', 'start' );

?>