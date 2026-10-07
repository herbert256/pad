<?php

  // Four rows, two of each dept, so sorting on dept alone leaves ties: a stable sort keeps
  // the rows of each dept in the order they came.

  $rows = [
    [ 'dept' => 'b', 'name' => 'zed' ],
    [ 'dept' => 'a', 'name' => 'yan' ],
    [ 'dept' => 'b', 'name' => 'amy' ],
    [ 'dept' => 'a', 'name' => 'bob' ],
  ];

?>
