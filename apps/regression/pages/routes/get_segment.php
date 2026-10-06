<?php

  // A {get} of a routed page asks for it with each segment encoded: a segment may hold what
  // a query string reads as its own - products/[id] takes a&b - and went into the address as
  // it was, so the page fetched was products/a, handed a value b; a&padStats would have
  // switched on what this server only lets its own requests switch on.

  $p = 'routes/products/a&b';

?>
