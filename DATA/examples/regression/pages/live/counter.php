<?php

  // A live counter on a page that has $padCsrf on (the application's _config/config.php
  // switches it on for this page): live/csrf.php fetches it and posts its events.

  $count = (int) padLiveValue ();

  if ( padLiveEvent () == 'add' )
    $count++;

?>
