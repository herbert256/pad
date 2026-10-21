<?php

  // padAbort from inside a loop whose row has a status, a message and a page of its own:
  // the 4xx page shows the refusal's, never the row's.

  $products = [ [ 'id' => 9, 'status' => 'draft', 'message' => 'supplier price 3.10', 'page' => 'x' ] ];

?>
