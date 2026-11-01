<?php

  // {printable:labels data='products', sheet='a4-3x8'}...{/printable:labels} - a sheet of
  // sticky labels: the content between the tags is one label, repeated over the data, laid
  // out in the grid of the sheet. sheet= the grid, named by its paper and its grid - a4-3x8
  // (63.5 by 33.9 mm, the default), a4-3x7, a4-2x7, a4-2x8, a4-2x4, a4-4x10, a4-5x13,
  // letter-3x10, letter-2x10, letter-2x5, letter-2x3 and letter-4x20; a sheet of your own with
  // across= and down=, width= and height= of a label, margin= the top and left margin and
  // pitch= the step from a label to the next, across and down, all in mm, paper= and
  // landscape. skip= leaves the first labels of a used sheet empty, copies= prints every label
  // that many times, outline prints the edges as well; padding= (2 mm), size= (9 pt) and
  // align= the label's text, set in the middle of its height.
  //
  //   {printable:labels data='people', sheet='a4-2x7', skip=3}
  //     <b>{$name}</b><br>{$street}<br>{$postcode} {$city}
  //   {/printable:labels}

  // The sheets: [ paper, across, down, width, height, pitch across, pitch down ] - the margins
  // centre the grid, as they do on these sheets.

  $padLabelSheets = [
    'a4-3x8'      => [ 'a4',     3,  8, 63.5,   33.9,  66,       33.9  ],
    'a4-3x7'      => [ 'a4',     3,  7, 63.5,   38.1,  66,       38.1  ],
    'a4-2x7'      => [ 'a4',     2,  7, 99.1,   38.1,  101.6,    38.1  ],
    'a4-2x8'      => [ 'a4',     2,  8, 99.1,   33.9,  101.6,    33.9  ],
    'a4-2x4'      => [ 'a4',     2,  4, 99.1,   67.7,  101.6,    67.7  ],
    'a4-4x10'     => [ 'a4',     4, 10, 48.5,   25.4,  48.5,     25.4  ],
    'a4-5x13'     => [ 'a4',     5, 13, 38.1,   21.2,  40.6,     21.2  ],
    'letter-3x10' => [ 'letter', 3, 10, 66.675, 25.4,  69.85,    25.4  ],
    'letter-2x10' => [ 'letter', 2, 10, 101.6,  25.4,  106.3625, 25.4  ],
    'letter-2x5'  => [ 'letter', 2,  5, 101.6,  50.8,  106.3625, 50.8  ],
    'letter-2x3'  => [ 'letter', 2,  3, 101.6,  84.667, 106.3625, 84.667 ],
    'letter-4x20' => [ 'letter', 4, 20, 44.45,  12.7,  52.07,    12.7  ],
  ];

  $padLabelGrid = padPrintableGrid ( $padLabelSheets, 'a4-3x8' );
  $padLabelBox  = padPrintableBox ( [ 'pad' => 2, 'size' => 9, 'align' => 'left', 'valign' => 'middle' ] );
  $padLabelEdge = padPrintableFlag ( 'outline' );

  return padPrintablePlan ( [

    'name' => [ 'label', 'labels' ],
    'grid' => $padLabelGrid,

    'item' => fn ( $cell, $parts ) => [
      $parts === NULL ? NULL : [ 'box', $cell [0], $cell [1], $cell [2], $cell [3], $parts [''], $padLabelBox ],
      padPrintableEdge ( $cell, $padLabelEdge ) ],

  ] );

?>
