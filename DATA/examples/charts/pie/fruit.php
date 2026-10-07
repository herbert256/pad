<?php

  // Crates sold at the market on a Saturday.

  $fruit = [];

  foreach ( [ 'Apples' => 120, 'Bananas' => 95, 'Pears' => 64, 'Oranges' => 88, 'Grapes' => 41,
              'Kiwis' => 23, 'Plums' => 18, 'Cherries' => 35, 'Mangos' => 12, 'Lemons' => 9,
              'Figs' => 6 ] as $kind => $crates )
    $fruit [] = [ 'kind' => $kind, 'crates' => $crates ];

?>
