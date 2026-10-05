<?php

  // The fixture of requests_old: the page a form posts to. Its check failed, so it keeps
  // the input for the next request and sends the browser back to the form - or, coming
  // from nowhere it knows, to the form all the same.

  padFlashInput ();

  padBack ( 'helpers/requests_old_form' );

?>
