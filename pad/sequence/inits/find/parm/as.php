<?php

  // Reads the positional parameter at place $pqFindPlace, the name of an action, as the option
  // of that name, for a tag whose own action is settled already: the parameter becomes that
  // option in $padParms, where actions/inits.php reads the options in the order written - in
  // {reverse '1..5', 'sum'} the sum after the reverse, as {reverse '1..5', sum} has them. A
  // number written right after it is its value, as parm/option.php gives one to an option
  // written so: {reverse '1..5', 'first', 2} is {reverse '1..5', first=2}.

  foreach ( $padParms [$pad] as $pqFindIdx => $pqFindOne )

    if ( $pqFindOne ['padPrmKind'] == 'parm' and $pqFindOne ['padPrmName'] === $pqFindPlace ) {

      $padParms [$pad] [$pqFindIdx] ['padPrmKind']  = 'option';
      $padParms [$pad] [$pqFindIdx] ['padPrmName']  = $pqFindParm;
      $padParms [$pad] [$pqFindIdx] ['padPrmValue'] = TRUE;

      $pqFindNext = $padParms [$pad] [ $pqFindIdx + 1 ] ?? NULL;

      if ( $pqFindNext and $pqFindNext ['padPrmKind'] == 'parm' and is_numeric ( $pqFindNext ['padPrmValue'] )
           and padFileExists ( PQ . "actions/parm/$pqFindParm" ) ) {
        $padParms [$pad] [$pqFindIdx] ['padPrmValue'] = $pqFindNext ['padPrmValue'];
        $pqFindTaken [ $pqFindNext ['padPrmName'] ] = TRUE;
      }

    }

?>
