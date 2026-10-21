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

    // The first item of {if}, {while}, {until}, {case} and {echo} is their whole expression,
    // and a bare word there is that expression - a constant, false, null - not a flag option
    // of the tag: {if false} set the option false, which the condition then read as TRUE and
    // rendered its content; {while false} ran its 10000 rounds, {echo PHP_VERSION} printed 1.
    //
    // So is $name = value there: the evaluator reads a single = as eq, as {elseif $x = 1} and
    // {if ($x = 1)} did, where the opening tag took it for a level variable - {if $role =
    // 'admin'} set $role to admin and then compared it with itself, TRUE for every role, and
    // {while $n = 5} looped its 10000 rounds.

    $padPrmWhole = ( ! count ( $padParms [$pad] ) and ( $padType [$pad] ?? '' ) == 'pad'
                     and in_array ( $padTag [$pad], [ 'if', 'while', 'until', 'case', 'echo' ], TRUE ) );

    if ( in_array    ( $padPrmName [0] ?? '', ['$','%'] ) and
         padValidVar ( substr ( $padPrmName, 1 ) )  and
         strlen      ( $padPrmValue ) and
         ! $padPrmCompare and ! $padPrmWhole )

      include PAD . 'level/parms/variable.php';

    elseif ( padValidVar ( $padPrmName ) and ! $padPrmCompare and ! padParmsRaw ()
             and ! ( $padPrmValue === '' and $padPrmWhole ) )

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

      if ( padFileExists ( PA . $padPrmChk ['padPrmName'] . '.php' )
           or padFileExists ( PT . $padPrmChk ['padPrmName'] )
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
