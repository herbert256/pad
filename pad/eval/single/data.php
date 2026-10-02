<?php

  // data: - returns a named block from the data store, where {data 'name'} definitions and the
  // data option keep their parsed arrays.

  global $padDataStore, $padCheckSyntax;

  // A name the store does not hold is named under the strict check, as the tag form is;
  // the lenient walk reads it as empty. It was a PHP error in both.

  if ( ! array_key_exists ( $name, $padDataStore ?? [] ) ) {

    if ( $padCheckSyntax )
      padError ( "there is no data store named '$name'" );

    return '';

  }

  return $padDataStore [$name];

?>