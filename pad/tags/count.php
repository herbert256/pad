<?php

  // {count 'name'} is an "is there anything in it" test: TRUE when the named data store,
  // or failing that the global array of that name, holds at least one element, so the
  // content renders only for non-empty data. It answers with a flag, not with a number -
  // the number of occurrences is the count@tag property.
  //
  // A global that is missing or NULL holds nothing, and one that is a plain value holds
  // that one value - what count() answered when this was written; PHP 8's count() throws
  // for both, and {count} ended the request instead.

  if ( isset ( $padDataStore [ $padParm ] ) )
    if ( count ( $padDataStore [ $padParm ] ) == 0 )
      return FALSE;
    else
      return TRUE;

  $padCountOf = $GLOBALS [ $padParm ] ?? NULL;

  if ( $padCountOf === NULL )
    return FALSE;

  if ( is_countable ( $padCountOf ) and count ( $padCountOf ) == 0 )
    return FALSE;
  else
    return TRUE;

?>