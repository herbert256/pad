<?php

  $groups  = typesByGroup ();
  $indexes = [
    [ 'page' => 'names',      'label' => 'By name',     'text' => 'Every type from A to Z, with its one line.' ],
    [ 'page' => 'order',      'label' => 'By order',    'text' => 'The order a bare name is tried in - {name} is the first type that has it.' ],
    [ 'page' => 'groups',     'label' => 'By group',    'text' => 'The types by what they reach - tags, stores, values, sequences, subsystems.' ],
    [ 'page' => 'cheatsheet', 'label' => 'Cheat sheet', 'text' => 'The syntax of every type on one page.' ] ];

  $memberCount = $exampleCount = 0;

  foreach ( typesCatalog () as $name => $type ) {
    $memberCount  += count ( typesMembers ( $name ) );
    $exampleCount += count ( typesExamples ( $name ) );
  }

?>
