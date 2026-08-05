<?php

  // Fires from padAtProperty() (pad/at/_lib/at.php) after an at/properties/<name>.php
  // handler has resolved an iteration property such as first@items or count@items.
  //
  // Xref only: files the property under both at/properties and properties.

  global $padInfoXref;

  if ( $padInfoXref  ) {
    padInfoXref  ( 'at', 'properties', $name );
    padInfoXref  ( 'properties', $name );
  }

?>