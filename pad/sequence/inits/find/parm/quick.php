<?php

  // Reads a sequence type out of the shape of the first parameter, for the forms that never
  // name one. Only runs when no type has been resolved yet.
  //
  // '1..10' means a range and '5;2;8' means a list, each taking the parameter with them. A
  // bare number left over means 'loop' with that many rows, which is what makes {sequence 5}
  // produce five values.
  //
  // The guards protect that last reading: it applies only to a numeric value that really was
  // the tag's first positional parameter, and never when the type declares flags/parm or the
  // action is one of actions/double/ or actions/parm/, since then the parameter is theirs.

  // A 0 is a count like any other - {sequence $n} with $n 0 asks for no values - so the
  // tests below are for 'nothing given', not for falsy.

  // A list is the values themselves - {sequence [3, 1, 2], sort} - which the list type takes
  // as written. It was cast to text and ended the request on "Array to string conversion".

  // Only where no store is pulled. Beside one - named by the prefix, the tag or an option, or
  // the store a {pull} takes - the store is the values, and the list is the parameter of an
  // action that takes stores, as append=[7, 8] is, and named for anything else: it took the
  // store's place, {action:append a, [7, 8]} and {pull:a [7, 8]} answered 7 8, a never read.

  if ( ! $pqSeq and is_array ( $pqFindParm ) ) {

    $pqFindStore = ( $pqPull or $pqTag == 'pull' or $pqType == 'pull' );

    foreach ( $padParms [$pad] as $pqFindOne )
      if ( $pqFindOne ['padPrmKind'] == 'option' and isset ( $pqStore [ $pqFindOne ['padPrmName'] ] ) )
        $pqFindStore = TRUE;

    if ( ! $pqFindStore ) {

      $pqSeq  = 'list';
      $pqParm = $pqFindParm;

    } elseif ( $pqAction and ( padFileExists ( PQ . "actions/double/$pqAction" ) or padFileExists ( PQ . "actions/merge/$pqAction" ) ) )

      $pqActionParm = pqActionList ( $pqAction, $pqFindParm ) ?? '';

    elseif ( $GLOBALS ['padCheckSyntax'] ?? FALSE )

      padError ( "the tag {" . $padTag [$pad] . "} was given a list as a parameter that nothing reads" );

    $pqFindParm = '';

  }

  if ( $pqSeq or is_array ( $pqFindParm ) or (string) $pqFindParm === '' )
    return;

  // Anywhere in the text: at its start the range lacks its first end, '..5', which padGetRange
  // names, where strpos () read 0 as no '..' and the range was reported as no sequence type.

  if ( str_contains ( $pqFindParm, '..' ) ) {
    $pqSeq      = 'range';
    $pqParm     = $pqFindParm;
    $pqFindParm = '';
  }

  if ( str_contains ( $pqFindParm, ';' ) ) {
    $pqSeq      = 'list';
    $pqParm     = $pqFindParm;
    $pqFindParm = '';
  }

      if ( (string) $pqFindParm === ''                                    ) return;
  elseif ( ! is_numeric ( $pqFindParm )                                   ) return;
  elseif ( ! $pqFindPositional                                            ) return;
  elseif ( $pqSeq    and padFileExists ( PT . "$pqSeq/flags/parm")  ) return;
  elseif ( $pqAction and padFileExists ( PQ . "actions/double/$pqAction") ) return;
  elseif ( $pqAction and padFileExists ( PQ . "actions/parm/$pqAction")   ) return;

  $pqSeq      = 'loop';
  $pqRows     = $pqFindParm;
  $pqFindParm = '';

?>
