<?php

  if ( $padPrmType [$pad] == 'close' ) 
    include 'level/close.php';

  foreach ( $padPrmParse as $padPrmOne ) {

    list ( $padPrmName, $padPrmValue ) = padSplit ( '=', trim ( $padPrmOne ) );

    if ( in_array    ( $padPrmName [0], ['$','%'] ) and 
         padValidVar ( substr ( $padPrmName, 1 ) )  and 
         strlen      ( $padPrmValue ) ) 

      include 'level/parms/variable.php';

    elseif ( padValidVar ( $padPrmName ) )
      
      include 'level/parms/option.php';
    
    else 
    
      include 'level/parms/parameter.php';
    
  }

  if ( ! isset ( $padOpt [$pad] [1] ) )
    $padOpt [$pad] [1] = '';

  if ( $padInfo ) 
    include 'events/parms.php';

?>