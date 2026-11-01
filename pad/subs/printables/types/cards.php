<?php

  // {printable:cards copies=10}...{/printable:cards} - business cards, 85 by 55 mm, ten to an
  // A4 sheet with crop marks in its margins to cut along: the content between the tags is one
  // card, repeated over the data - a card for every member of a team - or copies= times.
  // sheet= a4-2x5 (85 by 55 mm, the default) or letter-2x5 (3.5 by 2 inches), and the grid
  // options of the labels; padding= (5 mm), size= (8 pt) and align= the card's text, set in
  // the middle of its height.
  //
  //   {printable:cards copies=10}
  //     <h3>Ann Smith</h3>Developer<br>ann@example.org<br>+31 20 123 4567
  //   {/printable:cards}

  $padCardSheets = [
    'a4-2x5'     => [ 'a4',     2, 5, 85,   55,   85,   55   ],
    'letter-2x5' => [ 'letter', 2, 5, 88.9, 50.8, 88.9, 50.8 ],
  ];

  $padCardGrid = padPrintableGrid ( $padCardSheets, 'a4-2x5' );
  $padCardBox  = padPrintableBox ( [ 'pad' => 5, 'size' => 8, 'align' => 'left', 'valign' => 'middle' ] );

  return padPrintablePlan ( [

    'name'  => [ 'card', 'cards' ],
    'grid'  => $padCardGrid,

    'sheet' => fn ( $sheet ) => padPrintableCrop ( $padCardGrid ),

    'item'  => fn ( $cell, $parts ) => [
      $parts === NULL ? NULL : [ 'box', $cell [0], $cell [1], $cell [2], $cell [3], $parts [''], $padCardBox ],
      padPrintableEdge ( $cell ) ],

  ] );

?>
