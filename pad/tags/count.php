<?php

  // {count 'name'} is an "is there anything in it" test: TRUE when the named data store,
  // or failing that the global array of that name, holds at least one element, so the
  // content renders only for non-empty data. It answers with a flag, not with a number -
  // the number of occurrences is the count@tag property.

  if ( isset ( $padDataStore [ $padParm ] ) )
    if ( count ( $padDataStore [ $padParm ] ) == 0 )
      return FALSE;
    else
      return TRUE;

  if ( count ( $GLOBALS [ $padParm ] ) == 0 )
    return FALSE;
  else
    return TRUE;

?>