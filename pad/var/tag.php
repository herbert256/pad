<?php

  include 'inits.php';

  if ( ! padTagCheck ( $padFld ) ) 
    padError ( "Field '$padFld' not found" );

  $padVal = padTagValue ($padFld);

  return include 'exits.php';

?>