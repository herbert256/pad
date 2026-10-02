<?php

  // pull: - returns a stored sequence by name from $pqStore, the sequence subsystem's store of
  // sequences that {keep}/{make} have put aside.

  global $pqStore, $padCheckSyntax;

  // A name the store does not hold is named under the strict check, as the tag form is;
  // the lenient walk reads it as empty. It was a PHP error in both.

  if ( ! array_key_exists ( $name, $pqStore ?? [] ) ) {

    if ( $padCheckSyntax )
      padError ( "there is no stored sequence named '$name'" );

    return '';

  }

  return $pqStore [$name];

?>