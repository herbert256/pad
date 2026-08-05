<?php

  // Type handler for a field of the current data row ({field:name}, and the {$name} shorthand):
  // returns the field's value.
  //
  // A field that exists but is null returns NULL rather than '', which makes the level null so
  // that the null option and the @else@ branch can tell "no value" from "empty value".

  if ( padFieldNull ( $padTag [$pad]) )
    return NULL;

  return padFieldValue ( $padTag [$pad], 0 );

?>