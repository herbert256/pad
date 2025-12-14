<?php

  if ( $padTypeResult == 'eval' )
    return include PAD . 'level/parms/noParms.php';

  if ( $padPrmType [$pad] == 'close' )
    include PAD . 'level/close.php';

  $padParms [$pad]  = [];

  foreach ( $padPrmParse as $padPrmOne ) {

    padSplit ( '=', $padPrmOne, $padPrmName, $padPrmValue );

    $padParmsSet = '';

    if ( in_array    ( $padPrmName [0], ['$','%'] ) and
         padValidVar ( substr ( $padPrmName, 1 ) )  and
         strlen      ( $padPrmValue ) )

      include PAD . 'level/parms/variable.php';

    elseif ( padValidVar ( $padPrmName ) )

      include PAD . 'level/parms/option.php';

    else

      include PAD . 'level/parms/parameter.php';

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
    include PAD . 'events/parms.php';

?>