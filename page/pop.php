<?php
 
  foreach ( $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] as $key => $val ) {
    if ( isset ( $GLOBALS [$key] ) ) 
      unset ( $GLOBALS [$key] );
    $GLOBALS [$key] = $val;
  }

  $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] = [];
   
?>