<?php

  if ( $GLOBALS ['padInfoXref'] or $GLOBALS ['padInfoXapp'] )
    if ( in_array ( $type, [7,8] ) )
      padInfoXapp ( 'properties', $field );
  
?>