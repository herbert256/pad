<?php

  $title = 'PAD Manual';
  $description = 'User manual and documentation for the PAD framework';

  // The contents: the subjects by group, as the menu of the frame lists them.

  $manualContents = [];

  foreach ( manualTopics () as $manualGroup => $manualList ) {

    $manualTopics = [];

    foreach ( $manualList as $manualTopic )
      $manualTopics [] = [ 'topic' => $manualTopic, 'label' => manualLabel ( $manualTopic ) ];

    $manualContents [] = [ 'group' => $manualGroup, 'topics' => $manualTopics ];

  }

  unset ( $manualGroup, $manualList, $manualTopic, $manualTopics );

?>
