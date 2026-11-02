<?php
  return [
    'parameters' => [ 'rate' => [ 1, 1e-150, 1e150, FALSE ] ],
    'support' => 'nonnegative real',
    'work' => fn ( $p ) => 1,
    'sample' => fn ( $p, $random ) => -log1p ( -$random () ) / $p ['rate']
  ];
?>
