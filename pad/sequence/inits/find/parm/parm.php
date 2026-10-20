<?php

  // Last chance to place the leftover first parameter, once the sequence, the action and the
  // pull have all been resolved and both a type and an action may be in play.
  //
  // A type that declares flags/parm - one that genuinely takes an argument, such as multiply
  // or repeat - claims it first; then an action from actions/double/ or actions/parm/, which
  // are the actions that need one; otherwise whichever of the two is set gets it.

  if ( (string) $pqFindParm === '' )
    return;

      if ( $pqSeq    and padFileExists ( PT . "$pqSeq/flags/parm")  ) $pqParm       = $pqFindParm;
  elseif ( $pqAction and padFileExists ( PQ . "actions/double/$pqAction") ) $pqActionParm = $pqFindParm;
  elseif ( $pqAction and padFileExists ( PQ . "actions/parm/$pqAction")   ) $pqActionParm = $pqFindParm;
  elseif ( $pqSeq                                                         ) include PQ . 'inits/find/parm/none.php';
  elseif ( $pqAction and padFileExists ( PQ . "actions/merge/$pqAction")  ) $pqActionParm = $pqFindParm;
  elseif ( $pqAction                                                      ) include PQ . 'inits/find/parm/none.php';
  else                                                                      return;

  // Placed, it is no longer left over - parm/second.php reads what is.

  $pqFindParm = '';

?>
