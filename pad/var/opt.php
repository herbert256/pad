<?php

  include 'inits.php';

  if ( ! padFieldCheck ( $padFld ) ) 
    padError ( "Field '$padFld' not found" );

  $padOpts = array_merge ( $padDataDefaultStart, $padOpts );
  $padOpts = array_merge ( $padOpts, $padDataDefaultEnd   );

  $padVal = padFieldValue ($padFld);

  return include 'exits.php';

?>