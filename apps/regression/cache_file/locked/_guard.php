<?php

  // Opens the pages below only to a request bringing the header X-Key: open - a guard that
  // decides on something the cache knows nothing of.

  return ( $_SERVER ['HTTP_X_KEY'] ?? '' ) === 'open';

?>
