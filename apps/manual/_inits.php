<?php

  // The frame every PAD application shares (_common/_tags/padFrame.pad) instead of the
  // _common wrapper. Its menu is the manual's contents - the subjects by group
  // (_lib/topics.php), the page it is on marked.

  $commonFramed = TRUE;

  $frameMenu = [ [ 'link' => "{$padGo}index", 'label' => 'Contents', 'icon' => 'home', 'current' => $padPage == 'index' ] ];

  foreach ( manualTopics () as $manualGroup => $manualList ) {

    $frameMenu [] = [ 'head' => $manualGroup ];

    foreach ( $manualList as $manualTopic )
      $frameMenu [] = [ 'link'    => "{$padGo}pages/$manualTopic",
                        'label'   => manualLabel ( $manualTopic ),
                        'current' => $padPage == "pages/$manualTopic" ];

  }

  unset ( $manualGroup, $manualList, $manualTopic );

?>
