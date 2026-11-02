<?php

  $groups  = typesByGroup ();
  $indexes = [
    [ 'page' => 'names',      'label' => 'By name',     'text' => 'Every type from A to Z, with its one line.' ],
    [ 'page' => 'order',      'label' => 'Type prefixes', 'text' => 'Choose the namespace explicitly for every named tag.' ],
    [ 'page' => 'groups',     'label' => 'By group',    'text' => 'The types by what they reach - tags, stores, values, sequences, subsystems.' ],
    [ 'page' => 'cheatsheet', 'label' => 'Cheat sheet', 'text' => 'The syntax of every type on one page.' ] ];

  $memberCount = $exampleCount = 0;

  foreach ( typesCatalog () as $name => $type ) {
    $memberCount  += count ( typesMembers ( $name ) );
    $exampleCount += count ( typesExamples ( $name ) );
  }

?>
