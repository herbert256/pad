<?php

  global $padInfoXref;

  if ( $padInfoXref  ) {
    padInfoXref  ( 'at', 'properties', $name );
    padInfoXref  ( 'properties', $name );
  }

?>
