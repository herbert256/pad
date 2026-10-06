<?php

  // The fixture of routes/redirect_back: a route page that sends the browser back to itself
  // with padRedirect () when asked to.

  if ( isset ( $_GET ['again'] ) )
    padRedirect ();

?>
