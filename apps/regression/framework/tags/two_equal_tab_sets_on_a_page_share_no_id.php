<?php

  // Two tab sets with the same tabs on one page - a set per row - have no id in common:
  // the second is numbered -n2, which no tab of the first can be, where it was -2, the id
  // of the first set's second tab - and in a nested set a label then pointed at the outer
  // set's wrapper and its tab could not be clicked.

  include_once PAD . 'tags/_lib/tabs.php';

  $items = [ [ 'label' => 'A', 'content' => 'a' ], [ 'label' => 'B', 'content' => 'b' ] ];
  $html  = padUnprotect ( padTabs ( $items, 1 ) . padTabs ( $items, 1 ) );

  preg_match_all ( '/ id="([^"]+)"/', $html, $found );

  $ids    = count ( $found [1] );
  $shared = $ids - count ( array_unique ( $found [1] ) );

?>
