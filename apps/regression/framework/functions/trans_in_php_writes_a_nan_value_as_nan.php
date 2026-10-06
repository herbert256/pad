<?php

  // A NAN value - fdiv ( 0, 0 ) - fills its placeholder as NAN, the text the string helpers
  // make of it: cast with (string), PHP 8.5's "unexpected NAN value was coerced to string"
  // warning ended the request.

  $padLocale = 'nl';

  $r = json_encode ( [
    padTrans ( 'greeting', [ 'name' => fdiv ( 0, 0 ), 'place' => 'Leiden' ] ),
    padTrans ( 'cart.items', [ 'count' => fdiv ( 0, 0 ) ] )
  ] );

?>
