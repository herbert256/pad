<?php

  if ( $GLOBALS ['padInfoXref'] or $GLOBALS ['padInfoXapp'] ) {

    if ( $type == 7 and $value !== TRUE )
      padInfoXapp ( 'properties', $field );
  
    if ( $type == 8 and $value !== TRUE )
      padInfoXapp ( 'properties', $field );
  
  }
  
?>