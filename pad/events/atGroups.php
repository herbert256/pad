<?php

  // Fires from padAtGroup() (pad/at/_lib/at.php) just before an at/groups/<group>.php
  // handler tries to resolve a property@tag reference through that group - any, current,
  // level, options, parameters, providers, saved, variables or function.
  //
  // Xref only: files the group under both at/groups and properties in DATA/reference/.

  global $padInfoXref;

  if ( $padInfoXref  ) {
    padInfoXref  ( 'at', 'groups', $group );
    padInfoXref  ( 'properties',   $group );
  }

?>