<?php

  if ( $padPrmType [$pad] == 'close' and str_contains( $padOpt [$pad] [0], '}' ) ) 
    include '/pad/level/close.php';

  $padOptCnt = 0;

  foreach ( $padPrmParse as $padK => $padV ) {

    list ( $padPrmName, $padPrmValue ) = padSplit ( '=', trim($padV) );

    if ( str_starts_with ( $padPrmName, '$' ) or str_starts_with ( $padPrmName, '%' ) ) {

      $padSetName = substr ( $padPrmName, 1 );
      
      if ( padValidVar ( $padSetName ) and $padPrmValue ) {
        include '/pad/level/parms/variable.php';
        continue;
      }

    }

    if ( padValidVar ( $padPrmName ) )
      include '/pad/level/parms/option.php';
    else 
      include '/pad/level/parms/parameter.php';
    
  }

  if ( $GLOBALS ['padInfo'] ) 
    include '/pad/events/parms.php';

?>