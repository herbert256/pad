<?php

  // Live reload's poll - ?page&padReload from the script lib/reload.php adds to a local
  // page - is answered here with the newest file time, before the page cache or the page
  // itself are asked. Nothing happens unless $padReload is on and the request is local.

  if ( $padReload )
    padReloadAnswer ();

?>
