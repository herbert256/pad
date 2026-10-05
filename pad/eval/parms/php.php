<?php

  // Calls a plain PHP function - the handler behind the php: prefix.
  //
  // With no arguments written in the template the piped-in $value becomes the single argument,
  // so {echo $s | php:strtoupper} works; otherwise $parm is passed through as the argument
  // list and $value is ignored unless the template placed it there with @.

  if ( ! padPhpAllowed ( $name ) )
    return padError ( "the PHP function '$name' is not allowed by \$padPhpFunctions" );

  // An empty piped value is still the argument of a function that needs one: {$title |
  // ucfirst} with an empty $title called ucfirst() with nothing and ended the request. A
  // function that takes none - {echo php:time()} - is still called bare.

  if ( ! count ($parm) and ( $value !== '' or ( new ReflectionFunction ( $name ) ) -> getNumberOfRequiredParameters () ) )
    $parm [0] = $value;

  return call_user_func_array ($name, $parm);

?>