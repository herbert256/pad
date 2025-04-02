<?php

  if ( $padPrmType [$pad] == 'close' ) 
    include 'level/close.php';

  $padParms [$pad]  = [];
  
  foreach ( $padPrmParse as $padPrmOne ) {

    padSplit ( '=', $padPrmOne, $padPrmName, $padPrmValue );

    $padParmsSet = '';

    if ( in_array    ( $padPrmName [0], ['$','%'] ) and 
         padValidVar ( substr ( $padPrmName, 1 ) )  and 
         strlen      ( $padPrmValue ) ) 

      include 'level/parms/variable.php';

    elseif ( padValidVar ( $padPrmName ) )
      
      include 'level/parms/option.php';
    
    else 
    
      include 'level/parms/parameter.php';

    $padParms [$pad] [] = [
      'padPrmKind'  => $padParmsSetType,
      'padPrmName'  => $padParmsSetName,
      'padPrmValue' => $padParmsSetValue,
      'padPrmOrg'   => $padPrmOne
    ];

  }

  if ( ! isset ( $padOpt [$pad] [1] ) )
    $padOpt [$pad] [1] = '';

  if ( $padInfo ) 
    include 'events/parms.php';

?>