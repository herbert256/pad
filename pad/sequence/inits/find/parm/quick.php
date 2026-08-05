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

  if ( $pqSeq or ! $pqFindParm )
    return;

  if ( strpos( $pqFindParm, '..' ) ) {
    $pqSeq      = 'range';
    $pqParm     = $pqFindParm;
    $pqFindParm = '';
  }

  if ( strpos( $pqFindParm, ';' ) ) {
    $pqSeq      = 'list';
    $pqParm     = $pqFindParm;
    $pqFindParm = '';
  }

      if ( ! $pqFindParm                                                  ) return;
  elseif ( ! is_numeric ( $pqFindParm )                                   ) return;
  elseif ( ! isset ( $padParms [$pad] [0] ['padPrmKind'] )                ) return;
  elseif ( $padParms [$pad] [0] ['padPrmKind'] != 'parm'                  ) return;
  elseif ( $pqSeq    and file_exists ( PT . "$pqSeq/flags/parm")  ) return;
  elseif ( $pqAction and file_exists ( PQ . "actions/double/$pqAction") ) return;
  elseif ( $pqAction and file_exists ( PQ . "actions/parm/$pqAction")   ) return;

  $pqSeq      = 'loop';
  $pqRows     = $pqFindParm;
  $pqFindParm = '';

?>