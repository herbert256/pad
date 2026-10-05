<?php

  // The fixture of requests_session: keeps a cart in the session, which starts here.

  padSessionPut ( 'cart.items', [ 'apple', 'pear' ] );
  padSessionPut ( 'cart.coupon', 'SPRING' );

  $sessionPut = 'put ' . implode ( ',', padSession ( 'cart.items' ) );

?>
