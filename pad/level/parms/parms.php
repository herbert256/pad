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

    // A value that itself starts with '=' means the split landed inside == or ===: the
    // item is a comparison, {if $x == 1}, and goes whole to the parameter path to be
    // evaluated - not an assignment of '= 1' to $x, nor an option x.

    $padPrmCompare = str_starts_with ( $padPrmValue, '=' );

    if ( in_array    ( $padPrmName [0] ?? '', ['$','%'] ) and
         padValidVar ( substr ( $padPrmName, 1 ) )  and
         strlen      ( $padPrmValue ) and
         ! $padPrmCompare )

      include PAD . 'level/parms/variable.php';

    elseif ( padValidVar ( $padPrmName ) and ! $padPrmCompare and ! padParmsRaw () )

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

  // The same option written twice: one of the two silently wins. Strict mode says so.
  // A repeated sequence action or type is no mistake - {pull mySeq, add=4, add=10}
  // applies the action once per mention - so the sequence words stay out of it.

  if ( $padCheckSyntax ) {

    $padPrmSeen = [];

    foreach ( $padParms [$pad] as $padPrmChk ) {

      if ( $padPrmChk ['padPrmKind'] != 'option' )
        continue;

      if ( file_exists ( PA . $padPrmChk ['padPrmName'] . '.php' )
           or file_exists ( PT . $padPrmChk ['padPrmName'] )
           or in_array ( $padPrmChk ['padPrmName'], [ 'make', 'keep', 'remove', 'flag' ] ) )
        continue;

      if ( isset ( $padPrmSeen [ $padPrmChk ['padPrmName'] ] ) )
        padError ( "the option '" . $padPrmChk ['padPrmName'] . "' is written twice" );

      $padPrmSeen [ $padPrmChk ['padPrmName'] ] = TRUE;

    }

  }

  if ( ! isset ( $padOpt [$pad] [1] ) )
    $padOpt [$pad] [1] = '';

  if ( $padInfo )
    include PAD . 'events/parms.php';

?>