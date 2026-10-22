<?php

  // PHP's == holds 7 == '007' and '007' == '7.0' - three keys, one number.

  $rows = [ 7 => [ 'a' => 1 ], '007' => [ 'a' => 2 ], 'x' => [ 'a' => 3 ], '7.0' => [ 'a' => 4 ] ];

?>
