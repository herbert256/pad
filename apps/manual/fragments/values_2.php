<?php

  $user = [ 'name'     => 'ann smith',
            'nickname' => '',
            'city'     => NULL ];

  // The callback runs for a filled value only; a blank one gets
  // the default.

  $name     = padTransform ( $user ['name'],
                             fn ( $n ) => ucwords ( $n ),
                             'Guest' );
  $nickname = padTransform ( $user ['nickname'],
                             fn ( $n ) => "\"$n\"",
                             'no nickname' );

  // A default given as a Closure is made only when it is needed.

  $unknown = fn () => 'somewhere';
  $city    = padValue ( $user ['city'] ?? $unknown );

  // padTap looks at a value in passing and hands it on unchanged.

  $noted = '';
  $note  = function ( $sum ) use ( &$noted ) {
    $noted = "noted $sum";
  };

  $total = padTap ( array_sum ( [ 12.5, 30, 7.5 ] ), $note );

?>
