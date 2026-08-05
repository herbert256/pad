<?php

  // Fires from handling/handling.php for each handling option on a tag - the options that
  // have a pad/handling/types/<name>.php, such as sort or dedup - just before it is applied,
  // and records the name under 'options/handling' in the xref report.

  global $padInfoXref;

  if ( $padInfoXref  )
    padInfoXref ( 'options', 'handling', $padHandName );

?>