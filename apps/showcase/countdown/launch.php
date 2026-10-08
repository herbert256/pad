<?php

  // The launch: twelve days, five hours and a half from now - from half a minute ahead, so
  // the minutes come out round while the page renders.

  $launch = padNow ()->modify ( '+12 days 5 hours 30 minutes 30 seconds' )->format ( 'Y-m-d H:i:s' );

?>
