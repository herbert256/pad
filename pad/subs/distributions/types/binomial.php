<?php
  return [
    'parameters' => [ 'n' => [ 1, 0, 10000, TRUE ], 'p' => [ 0.5, 0, 1, FALSE ] ],
    'support' => 'integers 0..n',
    'work' => fn ( $p ) => max ( 1, $p ['n'] ),
    'sample' => function ( $p, $random ) {
      $sum = 0;
      for ( $i = 0; $i < $p ['n']; $i++ ) if ( $random () < $p ['p'] ) $sum++;
      return $sum;
    }
  ];
?>
