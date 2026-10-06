<?php

  // The page owns an array named rows; a request value of the same name arrived too. The
  // array is the data of {rows}, not the scalar 'rows' from the request.

  $_GET ['rows'] = 'evil';

  $rows = [ 'a', 'b' ];

?>
