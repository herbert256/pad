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
  // action is one of actions/parm/, since then the parameter is theirs. A number is no store
  // and no list, so for an action of actions/double/ it is the count as for any other - {append
  // 5} is {sequence 5, append}, as {sum 5} is {sequence 5, sum} - where append took it for a
  // store's name, skipped it and appended to the default 1..10; splice, whose first place is
  // an offset, is in actions/parm/ too.

  // A 0 is a count like any other - {sequence $n} with $n 0 asks for no values - so the
  // tests below are for 'nothing given', not for falsy.

  // A list is the values themselves - {sequence [3, 1, 2], sort} - which the list type takes
  // as written. It was cast to text and ended the request on "Array to string conversion".

  // Beside a store pulled - named by the prefix, the tag or an option, or the store a {pull}
  // takes - the list is the parameter of an action that takes stores, as append=[7, 8] is:
  // the action the prefix or the tag names, or else the first one written as a bare option,
  // whose value it becomes - {pull:a append, [7, 8]} is {pull:a append=[7, 8]}. It took the
  // store's place, {action:append a, [7, 8]} answered 7 8, a never read, and an action written
  // as an option ended the page on "a list as a parameter that nothing reads".
  //
  // Without such an action it is the values of a play over the store, as a ';' list is -
  // find/add.php makes the list type one: {keep:a [2,3,9]} keeps the terms of a that are 2, 3
  // or 9, as {keep:a '2;3;9'} does, where the strict check refused it.

  if ( ! $pqSeq and is_array ( $pqFindParm ) ) {

    $pqFindStore = ( $pqPull or $pqTag == 'pull' or $pqType == 'pull' );

    foreach ( $padParms [$pad] as $pqFindOne )
      if ( $pqFindOne ['padPrmKind'] == 'option' and isset ( $pqStore [ $pqFindOne ['padPrmName'] ] ) )
        $pqFindStore = TRUE;

    $pqFindTaker = NULL;

    if ( $pqFindStore and $pqAction
         and ( padFileExists ( PQ . "actions/double/$pqAction" ) or padFileExists ( PQ . "actions/merge/$pqAction" ) ) )
      $pqFindTaker = 'tag';

    elseif ( $pqFindStore )
      foreach ( $padParms [$pad] as $pqFindIdx => $pqFindOne )
        if ( $pqFindTaker === NULL and $pqFindOne ['padPrmKind'] == 'option' and $pqFindOne ['padPrmValue'] === TRUE
             and pqAction ( $pqFindOne ['padPrmName'] )
             and ( padFileExists ( PQ . 'actions/double/' . $pqFindOne ['padPrmName'] )
                   or padFileExists ( PQ . 'actions/merge/' . $pqFindOne ['padPrmName'] ) ) )
          $pqFindTaker = $pqFindIdx;

    if ( $pqFindTaker === 'tag' )

      $pqActionParm = pqActionList ( $pqAction, $pqFindParm ) ?? '';

    elseif ( $pqFindTaker !== NULL )

      $padParms [$pad] [$pqFindTaker] ['padPrmValue'] = $pqFindParm;

    else {

      $pqSeq  = 'list';
      $pqParm = $pqFindParm;

    }

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
  elseif ( $pqAction and padFileExists ( PQ . "actions/parm/$pqAction")   ) return;

  $pqSeq      = 'loop';
  $pqRows     = $pqFindParm;
  $pqFindParm = '';

?>
