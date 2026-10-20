<?php

  // A number written right after an option that names a type taking a parameter or an action
  // that counts, the option written bare, is that option's value: {sequence '1..3', add, 2} is
  // {sequence '1..3', add=2}, {pull:a first, 2} is {pull:a first=2}, as {sequence add, 3} gave
  // add its 3. Nothing read it: {pull:a first, 2} took one term and {pull:a add, 2} added 1,
  // without a word, and {sequence '1..3', add, 2} ended on "a parameter '2' that nothing reads".
  //
  // The value goes into the option where plays/inits.php, actions/inits.php and find/loop.php
  // read it, and the places taken - by $padOpt index - are left out of the rest of find/:
  // $pqFindParm for the first, parm/second.php for the others.

  $pqFindTaken = [];

  foreach ( $padParms [$pad] as $pqFindAt => $pqFindOne ) {

    $pqFindBefore = $padParms [$pad] [$pqFindAt - 1] ?? NULL;

    if ( $pqFindOne ['padPrmKind'] != 'parm' or ! is_numeric ( $pqFindOne ['padPrmValue'] )
         or ! $pqFindBefore or $pqFindBefore ['padPrmKind'] != 'option' or $pqFindBefore ['padPrmValue'] !== TRUE )
      continue;

    $pqFindName = $pqFindBefore ['padPrmName'];

    if ( ( pqSeq ( $pqFindName ) and padFileExists ( PT . "$pqFindName/flags/parm" ) )
         or ( pqAction ( $pqFindName ) and padFileExists ( PQ . "actions/parm/$pqFindName" ) ) ) {

      $padParms [$pad] [$pqFindAt - 1] ['padPrmValue'] = $pqFindOne ['padPrmValue'];

      $pqFindTaken [ $pqFindOne ['padPrmName'] ] = TRUE;

    }

  }

  if ( isset ( $pqFindTaken [1] ) )
    $pqFindParm = '';

?>
