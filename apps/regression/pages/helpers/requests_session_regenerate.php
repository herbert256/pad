<?php

  // The fixture of requests_session: a new session id, the data moved along.

  $sessionNew = ( padSessionRegenerate () ? 'regenerated' : 'not regenerated' ) . ' ' . padSession ( 'cart.items.0', 'empty' );

?>
