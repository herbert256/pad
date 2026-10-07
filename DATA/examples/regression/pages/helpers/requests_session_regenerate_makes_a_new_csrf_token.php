<?php

  // A login moves the session to a new id, and its CSRF token goes with the old id: a token
  // someone learned before the login - the session they planted on the visitor was their
  // own - is worth nothing after it, the way the id is. The token moved along with the data,
  // and the forms of the logged-in visitor could be posted from another site.

  $tokenBefore = padCsrfToken ();
  $tokenMoved  = padSessionRegenerate ();
  $tokenAfter  = padCsrfToken ();

  $tokenResult = ( $tokenMoved ? 'regenerated' : 'not regenerated' ) . ', '
               . ( $tokenAfter === $tokenBefore ? 'the same token' : 'a new token' ) . ', '
               . ( preg_match ( '/^[0-9a-f]{64}$/', $tokenAfter ) ? 'well formed' : 'malformed' );

?>
