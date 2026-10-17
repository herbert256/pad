<?php

  // A date object a page's PHP leaves is its text - Y-m-d H:i:s, as {$d} writes it - to an
  // operator, a pipe that takes text and a query value: compared as an object it stood above
  // every string, and | upper, . and {?d} ended the page on a TypeError.

  $d = new DateTime ( '2020-01-02 03:04:05' );

?>
