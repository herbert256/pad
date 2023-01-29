<?php

  include 'inits.php';

  if ( ! padFieldCheck ( $padFld ) ) 
    padError ( "Field '$padFld' not found" );
  
  $padVal = padFieldValue ($padFld);

  return include 'exits.php';

?>