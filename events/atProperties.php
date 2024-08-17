<?php

  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) {  
    padInfoXapp  ( 'at', 'properties', $name );
    padInfoXapp  ( 'properties', $name );
  }

?>