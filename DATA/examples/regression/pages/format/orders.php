<?php

  // The page the other tests here ask for data. Rendered as a page it is a list; asked for
  // JSON or CSV it answers the two variables it names in $padExpose - and never $secret.

  $orders = [ [ 'id' => 1, 'customer' => 'Alice',    'amount' => 12.5 ],
              [ 'id' => 2, 'customer' => 'Bob, Jr.', 'amount' => 7    ] ];
  $total  = 19.5;
  $secret = 'stays on the server';

  $padExpose = [ 'orders', 'total' ];

?>
