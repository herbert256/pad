<?php

  // The flash fixture: a message for the next request, and the redirect that leads there.

  padFlash ( 'Your message was sent.', 'success' );

  padRedirect ( 'request/flashed' );

?>
