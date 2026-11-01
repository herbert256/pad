<?php

  $seeds = [];

  foreach ( [ 'dots', 'voronoi', 'topography', 'grain' ] as $kind )
    $seeds [] = [ 'kind' => $kind, 'same' => padPattern ( $kind, [ 'seed' => 1, 'as' => 'url' ] ) === padPattern ( $kind, [ 'seed' => 2, 'as' => 'url' ] ) ? 'same' : 'differs' ];

?>
