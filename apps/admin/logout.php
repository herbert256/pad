<?php

  // Logging out is a post - the menu's form, with the CSRF token - so a link or an image on
  // another page cannot end the session; anything else just goes back to the login page.

  if ( adminPost () ) {
    $adminUser  = NULL;
    $adminStamp = NULL;
  }

  padRedirect ( 'login' );

?>
