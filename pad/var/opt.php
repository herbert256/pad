<?php

  include 'inits.php';

  $padOpts = array_merge ( $padDataDefaultStart, $padOpts );
  $padOpts = array_merge ( $padOpts, $padDataDefaultEnd   );

  if ( ! padFieldCheck ( $padFld ) ) 
    padError ( "Field '$padFld' not found" );

  $padVal = padFieldValue ($padFld);

  return include 'exits.php';

?>