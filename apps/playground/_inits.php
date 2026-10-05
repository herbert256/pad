<?php

  // The playground renders whatever template it is sent: a local request only - the command
  // line, or this machine asking itself with nothing forwarded (padLocal). Anyone else is
  // answered 403 before a page of it runs, the way inits/page.php answers a 404.

  if ( ! padLocal () )
    padAbort ( 403, 'The PAD playground runs the code it is given, so it answers this machine only.' );

?>
