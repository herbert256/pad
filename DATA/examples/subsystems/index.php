<?php

  $groups = subsystemsByGroup ();
  $indexes = [
    [ 'page' => 'names', 'label' => 'By name', 'text' => 'Every subsystem from A to Z, with its description.' ],
    [ 'page' => 'groups', 'label' => 'By group', 'text' => 'Tag subsystems, and kinds selected through options or helpers.' ],
    [ 'page' => 'cheatsheet', 'label' => 'Cheat sheet', 'text' => 'The syntax of every subsystem on one page.' ] ];

  $memberCount = $exampleCount = 0;
  foreach ( subsystemsCatalog () as $name => $subsystem ) {
    $memberCount += count ( subsystemsMembers ( $name ) );
    $exampleCount += count ( subsystemsExamples ( $name ) );
  }

?>
