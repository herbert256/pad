<?php

  // Rows whose columns are named like the error page's own values - id, status, message,
  // page - and a field of the loop that fails: the 500 page shows the request's status and
  // id, never the row's.

  $orders = [ [ 'id' => 'order-17', 'status' => 'shipped', 'message' => 'private note', 'page' => 'secret' ] ];

?>
