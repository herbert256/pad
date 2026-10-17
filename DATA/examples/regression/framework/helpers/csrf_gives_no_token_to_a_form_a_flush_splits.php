<?php

  // The CSRF pass gives no token to a form whose start a {flush} sends while the rest of it -
  // its button, which may send it to another site - is still to come; an ordinary form
  // posting here still gets one.

  $csrfCase = fn ( $html, $part = FALSE ) => str_contains ( padCsrfForms ( $html, TRUE, $part ), 'padCsrfToken' ) ? 'token' : 'none';

  $csrfShown = $csrfCase ( '<form method="post"><input name="a">', TRUE ) . ' ' . $csrfCase ( '<form method="post"><input name="a"><button>Go</button></form>', TRUE );

?>
