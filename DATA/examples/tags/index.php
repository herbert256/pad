<?php

  $groups   = tagsByGroup ();
  $indexes  = [
    [ 'page' => 'names',      'label' => 'By name',     'text' => 'Every tag from A to Z, with its one line.' ],
    [ 'page' => 'groups',     'label' => 'By group',    'text' => 'The tags by what they are for - conditions, loops, forms, charts ...' ],
    [ 'page' => 'forms',      'label' => 'By form',     'text' => 'Single tags, tag pairs, and the tags that can be either.' ],
    [ 'page' => 'options',    'label' => 'By option',   'text' => 'Every option a tag takes of its own, and the tags that take it.' ],
    [ 'page' => 'cheatsheet', 'label' => 'Cheat sheet', 'text' => 'The syntax of every tag on one page.' ] ];

  $exampleCount = 0;
  foreach ( tagsCatalog () as $name => $tag )
    $exampleCount += count ( tagsExamples ( $name ) );

?>
