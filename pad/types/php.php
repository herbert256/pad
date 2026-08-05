<?php

  // Type handler for a PHP function called as a tag ({php:strlen 'abc'}): calls the function
  // named by the tag and returns its result as the tag's value.
  //
  // $padOpt [$pad] [0] holds the raw parameter text, the entries after it the parsed arguments;
  // with no parameters written the function is called with no arguments at all.

  if ( ! strlen ( $padOpt [$pad] [0] ) )
    return call_user_func_array ( $padTag [$pad], [] );

  $padUserFunc = $padOpt [$pad];

  unset ( $padUserFunc [0] );

  return call_user_func_array ( $padTag [$pad], $padUserFunc );

?>