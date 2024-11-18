<?php

  if ( $padPrmType [$pad] == 'close' ) 
    include PAD . 'level/close.php';

  foreach ( $padPrmParse as $padPrmOne ) {

    list ( $padPrmName, $padPrmValue ) = padSplit ( '=', trim ( $padPrmOne ) );

    if ( in_array    ( $padPrmName [0], ['$','%'] ) and 
         padValidVar ( substr ( $padPrmName, 1 ) )  and 
         strlen      ( $padPrmValue ) ) 

      include PAD . 'level/parms/variable.php';

    elseif ( padValidVar ( $padPrmName ) )
      
      include PAD . 'level/parms/option.php';
    
    else 
    
      include PAD . 'level/parms/parameter.php';
    
  }

  if ( ! isset ( $padOpt [$pad] [1] ) )
    $padOpt [$pad] [1] = '';

  if ( $GLOBALS ['padInfo'] ) 
    include PAD . 'events/parms.php';

?>