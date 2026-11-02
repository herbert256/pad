<?php
  return [
    'parameters' => [ 'p' => [ 0.5, 0, 1, FALSE ] ],
    'support' => '{0,1}',
    'work' => fn ( $p ) => 1,
    'sample' => fn ( $p, $random ) => $random () < $p ['p'] ? 1 : 0
  ];
?>
