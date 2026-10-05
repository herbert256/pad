<?php

  padSessionPut ( 'cart.items',  [ 'apple', 'pear' ] );
  padSessionPut ( 'cart.coupon', 'SPRING' );

  $items  = implode ( ', ', padSession ( 'cart.items' ) );
  $coupon = padSessionPull ( 'cart.coupon' );               // read, and forget
  $again  = padSessionHas  ( 'cart.coupon' ) ? 'yes' : 'no';
  $theme  = padSession     ( 'theme', 'light' );            // not there: the default

  padSessionForget ( 'cart' );

?>
