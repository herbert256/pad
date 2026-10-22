<?php

  // Type handler for a PHP function called as a tag ({php:strlen 'abc'}): calls the function
  // named by the tag and returns its result as the tag's value.
  //
  // $padOpt [$pad] [0] holds the raw parameter text, the entries after it the parsed arguments;
  // with no parameters written the function is called with no arguments at all.

  if ( ! padPhpAllowed ( $padTag [$pad] ) )
    return padError ( "the PHP function '" . $padTag [$pad] . "' is not allowed by \$padPhpFunctions" );

  // The tag form takes a bare function name and its arguments after a space -
  // {php:strlen 'abc'}; a call written out with ( ) is the expression form,
  // {echo $x | php:strlen(@)}. As a tag the ( ) stayed in the name, and {php:strlen(@)} or
  // {php:intdiv(1,0)} ended on a raw call_user_func_array TypeError.

  if ( str_contains ( $padTag [$pad], '(' ) )
    return padError ( "the php: tag takes a bare function name and its arguments after a space - {php:"
                      . strstr ( $padTag [$pad], '(', TRUE ) . " ...}; a call with ( ) is the expression form" );

  // db() and padDb() take SQL as their first parameter, which a value spliced in judges where
  // it lands (padDbSplice, lib/db.php) - and one more than 4 KB past the tag's {, where the
  // splice looks back no further, the tag judges itself, as a database tag does
  // (padDbSpliceFar): {php:db "FIELD ... where 1 = 1 <4200 spaces> and salary < {$max}"}
  // with ?max=0 or 1 counted every row.

  if ( in_array ( strtolower ( $padTag [$pad] ), [ 'db', 'paddb' ], TRUE ) and ! padDbSpliceFar () )
    return FALSE;

  if ( ! strlen ( $padOpt [$pad] [0] ) )
    return call_user_func_array ( $padTag [$pad], [] );

  $padUserFunc = $padOpt [$pad];

  unset ( $padUserFunc [0] );

  // A callable among the arguments is a call of its own, held to the same list as the
  // expression form is (eval/parms/php.php): under a $padPhpFunctions list a listed
  // dispatcher - {php:call_user_func 'strtoupper', ...} - could otherwise reach an unlisted
  // function.

  if ( $padPhpRefused = padPhpCallables ( $padTag [$pad], $padUserFunc ) )
    return padError ( $padPhpRefused );

  return call_user_func_array ( $padTag [$pad], $padUserFunc );

?>
