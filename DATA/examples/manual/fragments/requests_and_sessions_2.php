<?php

  padSessionPut ( 'cart.items',  [ 'apple', 'pear' ] );
  padSessionPut ( 'cart.coupon', 'SPRING' );

  $items  = implode ( ', ', padSession ( 'cart.items' ) );

  // Read, and forget.

  $coupon = padSessionPull ( 'cart.coupon' );
  $again  = padSessionHas  ( 'cart.coupon' ) ? 'yes' : 'no';

  // Not there: the default.

  $theme  = padSession ( 'theme', 'light' );

  padSessionForget ( 'cart' );

?>
