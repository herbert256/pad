<?php

  // Fires in padField() (pad/lib/field/field.php) at the end of the branch that resolves a
  // plain field name - the one without @ or . - through prefixes, options, tags and level
  // data, after the fallback lookups have run.
  //
  // Xref only: for the tag lookups (type 7 padTagCheck, type 8 padTagValue) it files the
  // name under properties, skipping hits whose value is just a boolean TRUE.

  global $padInfoXref;

  if (  $padInfoXref ) {

    if ( $type == 7 and $value !== TRUE )
      padInfoXref ( 'properties', $field );

    if ( $type == 8 and $value !== TRUE )
      padInfoXref ( 'properties', $field );

  }

?>