<?php

  // One ink lays every brick in it - a tile the seed does not change; with more the seed picks
  // each brick's ink.

  $same = fn ( $colors ) => padPattern ( 'brick', [ 'seed' => 1, 'colors' => $colors, 'as' => 'url' ] )
                        === padPattern ( 'brick', [ 'seed' => 2, 'colors' => $colors, 'as' => 'url' ] ) ? 'the same for every seed' : 'another for another seed';

  $one     = $same ( '#fff, #a00' );
  $several = $same ( '#fff, #a00, #0a0, #00a' );

?>
