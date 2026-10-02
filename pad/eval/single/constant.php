<?php

  // constant: - returns the value of the PHP constant called $name, which includes PAD's own
  // path constants such as APP and DATA.

  global $padCheckSyntax;

  // An undefined constant is named under the strict check, as the tag form is; the
  // lenient walk reads it as empty. constant() threw an uncaught Error in both.

  if ( ! defined ( $name ) ) {

    if ( $padCheckSyntax )
      padError ( "there is no constant named '$name'" );

    return '';

  }

  return constant ($name);

?>