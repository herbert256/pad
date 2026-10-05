<?php

  // Asked with ?back, the page sends the visitor back to itself: padRedirect() with no
  // page goes to the name the page was asked by, in the clean form.

  if ( isset ( $back ) )
    padRedirect ();

?>
