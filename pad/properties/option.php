<?php

  // The option:name@tag property: the value of one named option of level $padIdx, or NULL
  // when the tag was not given it.
  //
  // Named options - {items sort="name" reverse} - are kept in $padPrm, a bare one holding
  // the value TRUE. $parm is the option name and comes from the including function, not
  // from a global; options.php returns them all at once.

  global $padPrm;

  if ( isset ( $padPrm [$padIdx] [$parm] ) )
    return $padPrm [$padIdx] [$parm];
  else
    return NULL;

?>