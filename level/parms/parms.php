<?php

  if ( $padPrmType [$pad] == 'close' ) 
    include '/pad/level/close.php';

  foreach ( $padPrmParse as $padPrmOne ) {

    list ( $padPrmName, $padPrmValue ) = padSplit ( '=', trim ( $padPrmOne ) );

    if ( in_array    ( $padPrmName [0], ['$','%'] ) and 
         padValidVar ( substr ( $padPrmName, 1 ) )  and 
         strlen      ( $padPrmValue ) ) 

      include '/pad/level/parms/variable.php';

    elseif ( padValidVar ( $padPrmName ) )
      
      include '/pad/level/parms/option.php';
    
    else 
    
      include '/pad/level/parms/parameter.php';
    
  }

  if ( ! isset ( $padOpt [$pad] [1] ) )
    $padOpt [$pad] [1] = '';

  if ( $GLOBALS ['padInfo'] ) 
    include '/pad/events/parms.php';

?>