<?php

  // The form came back - a field elsewhere in it may have failed its rules - and the list
  // shows its items in the order posted: c at 1, a and b both at 2, which keep the order
  // they had. The post is set here, so the case is one fetch.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST ['padForm'] = 'r';
  $_POST ['order']   = [ 'a' => '2', 'b' => '2', 'c' => '1' ];

?>
