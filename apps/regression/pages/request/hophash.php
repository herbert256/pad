<?php

  // A fixture: padRedirect to a page with a #fragment and a value. An action page, so the
  // walker leaves it out; request/redirect_hash fetches it deliberately.

  padRedirect ( 'request/vars#top', [ 'x' => 1 ] );

?>
