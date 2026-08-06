<?php

  // Parses the tag's comma-separated option list into this level's parameter state.
  //
  // Options taken from a closing tag are resolved first by level/close.php. Each item is
  // split on '=' and routed by its shape: $name / %name assignments go to
  // parms/variable.php, plain identifiers to parms/option.php, everything else to
  // parms/parameter.php as a positional parameter - filling $padSetLvl/$padSetOcc, $padPrm
  // and $padOpt respectively. Every item also leaves a record in $padParms [$pad], which
  // handling/ and the trace output walk.

  if ( $padPrmType [$pad] == 'close' )
    include PAD . 'level/close.php';

  $padParms [$pad]  = [];

  foreach ( $padPrmParse as $padPrmOne ) {

    padSplit ( '=', $padPrmOne, $padPrmName, $padPrmValue );

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