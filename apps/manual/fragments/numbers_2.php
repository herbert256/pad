<?php

  // Counters, a sentence and a download size, for people.

  $stats = [];

  foreach ( [ 950, 1500, 48210, 1534200, 2500000000 ] as $number )
    $stats [] = [ 'number' => $number,
                  'short'  => padNumberAbbreviate ( $number, 1 ),
                  'words'  => padNumberForHumans  ( $number, 1 ) ];

  $download = padNumberFileSize ( 5347737, 1 );

?>
