<?php

  // padValue calls a Closure with the arguments; anything else - a string that happens to
  // name a PHP function too - is answered as it is.

  $r = json_encode ( [
    padValue ( 5 ),
    padValue ( fn ( $a, $b ) => $a + $b, 2, 3 ),
    padValue ( fn () => 'made' ),
    padValue ( 'strtoupper', 'x' ),
    padValue ( NULL ),
    padValue ( [ 1, 2 ] ),
    padValue ( FALSE )
  ] );

?>
