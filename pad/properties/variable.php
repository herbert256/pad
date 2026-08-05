<?php

  // The variable:name@tag property: the value of one level variable of level $padIdx, or
  // NULL when it is not set there.
  //
  // Level variables - {users $total = 0}, constant for the whole level - are kept per
  // level in $padSetLvl. $parm is the variable name and comes from the including function,
  // not from a global; variables.php returns them all at once.

  global $padSetLvl;

  if ( isset ( $padSetLvl [$padIdx] [$parm] ) )
    return $padSetLvl [$padIdx] [$parm];
  else
    return NULL;

?>