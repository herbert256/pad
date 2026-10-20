<?php

  // Picks up the tag's first positional parameter as $pqFindParm - the value the rest of
  // find/ has to attach to whichever of the sequence or the action it decides was meant.

  $pqFindParm = $padOpt [$pad] [1];

  // The place of the parameter find/ is reading, in $padOpt - parm/second.php moves it on.

  $pqFindPlace = 1;

  // Whether that parameter named what the tag runs - parm/name.php, a {pull}'s store in
  // find/pull.php - so that a second one is the first parameter of what it named
  // (parm/second.php).

  $pqFindNamed = FALSE;

  // Whether a name of an action is read as the option of that name, parm/as.php, rather than
  // as the action the tag runs - parm/second.php sets it after a count, a range or a list.

  $pqFindAsOption = FALSE;

  // Whether that parameter is the tag's own, a count or a range of the tag: written before any
  // type, store or action the tag names. Only the first item could be: {sequence name='i', 5}
  // took the 5 for no count and reported it as "is not a sequence type". Written after a type,
  // store or action, it is theirs - {make add=3, mySequence, 3} gives add its 3.

  $pqFindPositional = FALSE;

  foreach ( $padParms [$pad] as $pqFindOne )
    if ( $pqFindOne ['padPrmKind'] == 'parm' ) {
      $pqFindPositional = TRUE;
      break;
    } elseif ( $pqFindOne ['padPrmKind'] == 'option'
               and ( pqSeq ( $pqFindOne ['padPrmName'] ) or pqAction ( $pqFindOne ['padPrmName'] )
                     or isset ( $pqStore [ $pqFindOne ['padPrmName'] ] ) ) )
      break;

?>
