<?php

  // flag: - returns a named boolean from the bool store that the toBool option and {bool ...}
  // fill in. Note this reads the bool store, not the sequence flags that the tag-level
  // {flag:...} type reaches through the sequence subsystem.

  global $padBoolStore, $padCheckSyntax;

  // A name the store does not hold is named under the strict check, as the tag form is;
  // the lenient walk reads it as empty. It was a PHP error in both.

  if ( ! array_key_exists ( $name, $padBoolStore ?? [] ) ) {

    if ( $padCheckSyntax )
      padError ( "there is no bool store named '$name'" );

    return '';

  }

  return $padBoolStore [$name];

?>