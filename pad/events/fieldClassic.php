<?php

  if (  $GLOBALS ['padInfoXref'] ) {

    if ( $type == 7 and $value !== TRUE )
      padInfoXref ( 'properties', $field );
  
    if ( $type == 8 and $value !== TRUE )
      padInfoXref ( 'properties', $field );
  
  }
  
?>