<?php

  if ( $GLOBALS ['padInfoXref'] or $GLOBALS ['padInfoXapp'] ) {

    if ( $type == 7 and $return === TRUE )
      padInfoXapp ( 'properties', $field );
  
    if ( $type == 8 and $return )
      padInfoXapp ( 'properties', $field );
  
  }
  
?>