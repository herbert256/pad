<?php

  // content: - returns a named block from the content store, where the content option and the
  // {content} tags park rendered output for later reuse.

  global $padContentStore, $padCheckSyntax;

  // A name the store does not hold is named under the strict check, as the tag form is;
  // the lenient walk reads it as empty. It was a PHP error in both. Inside a sandbox a name
  // the pass hid misses quietly, as the tag form's does (padStrHidden).

  if ( ! array_key_exists ( $name, $padContentStore ?? [] ) ) {

    if ( $padCheckSyntax and ! padStrHidden ( $name ) )
      padError ( "there is no content block named '$name'" );

    return '';

  }

  return $padContentStore [$name];

?>
