<?php

  // The fixture of requests_session: reads the cart and takes the coupon out of it. Without
  // the session cookie there is nothing to read, and no session is started for that.

  $sessionRead = implode ( ',', padSession ( 'cart.items', [ 'nothing' ] ) )
               . ' ' . padSessionPull ( 'cart.coupon', 'no coupon' )
               . ' ' . ( session_status () === PHP_SESSION_ACTIVE ? 'open' : 'none' );

?>
