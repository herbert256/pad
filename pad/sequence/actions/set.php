<?php

  // Sets up an action invoked from an expression rather than from a tag:
  // eval/parms/sequence.php parks the call in $pqSetAction/$pqSetParms and start/eval/
  // arrives here.
  //
  // The first parameter decides where the data comes from - an array is taken as the
  // sequence itself ($pqBuild 'given', $pqFixed), anything else names a stored sequence
  // to pull ($pqBuild 'pull', $pqPull). The remaining parameters are joined into the '|'
  // string the action handlers expect; array parameters are first parked in $pqStore
  // under a generated __action__<random> key so they can be referred to by name.

  $pqAction     = $pqSetAction;
  $pqActionParm = '';
  $pqParms      = [];
  $pqFirst      = pqActionArray ( $pqSetParms );

  // A number is a value to act on, a list of one: no store is named by a number, and 5 |
  // sequence:sum ended on "there is no stored sequence named '5'".
  //
  // So is every number written right after it, for an action that takes neither a count nor
  // a store to act with: the values of sequence:sum(1, 2, 3) are 1, 2 and 3, as PHP's max(1,
  // 2, 3) reads its arguments - only the first was a value, the others a parameter sum drops,
  // and it answered [1] without a word; sort(3, 1, 2) answered [3], average(2, 4) [2]. A
  // number names no store, so sort, reverse and dedup, which fold stores in, read them as
  // values too; an action that takes a count or a store to act with - first, shift,
  // intersection, append - has it after its first value, sequence:first(5, 2).

  $pqSetNumbers = ( ! padFileExists ( PQ . "actions/parm/$pqAction" )
                    and ! padFileExists ( PQ . "actions/double/$pqAction" ) );

  if ( is_int ( $pqFirst ) or is_float ( $pqFirst ) or ( is_string ( $pqFirst ) and is_numeric ( $pqFirst ) ) ) {

    $pqFirst = [ $pqFirst ];

    foreach ( $pqSetNumbers ? $pqSetParms : [] as $pqV )
      if ( is_int ( $pqV ) or is_float ( $pqV ) or ( is_string ( $pqV ) and is_numeric ( $pqV ) ) ) {
        $pqFirst [] = $pqV;
        array_shift ( $pqSetParms );
      } else
        break;

  }

   if ( is_array ( $pqFirst )  ) {

    // The values acted on are values: a list among them ended the request on "Array to
    // string conversion" in dedup, distinct, merge and the rest.

    foreach ( $pqFirst as $pqFirstOne )
      if ( ! is_scalar ( $pqFirstOne ) ) {
        padError ( "$pqAction() acts on a list of values, not a list of lists" );
        $pqFirst = array_filter ( $pqFirst, 'is_scalar' );
        break;
      }

    $pqBuild = 'given';
    $pqFixed = $pqFirst;

   } else {

    $pqBuild = 'pull';
    $pqPull  = $pqFirst;

  }

  // A parameter given to an action that takes none - sum, count, average - is named by the
  // strict check, as a list given to one is ("sum= takes no list") and as the tag form names it
  // (inits/find/parm/none.php): sequence:sum([1, 2], 3) answered [3] without a word.

  $pqSetNone = ( $pqSetNumbers and ! padFileExists ( PQ . "actions/merge/$pqAction" ) );

  foreach ( $pqSetParms as $pqV )

    if ( is_array ( $pqV ) ) {

      $pqT = pqActionList ( $pqAction, $pqV );

      if ( $pqT !== NULL )
        $pqActionParm .= $pqT . '|';

    } else {

      if ( $pqSetNone and $pqV !== NULL and $pqV !== '' and ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) )
        padError ( "The $pqAction action takes no parameter after the values it acts on, and '"
                 . ( is_scalar ( $pqV ) ? $pqV : gettype ( $pqV ) ) . "' is one" );

      $pqActionParm .= $pqV . '|';

    }

?>
