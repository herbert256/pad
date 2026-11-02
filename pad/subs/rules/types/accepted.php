<?php

  // accepted - a box that must be ticked, terms and conditions: 1, on, yes or true, in any
  // case. An empty field is judged by it, so a box left unticked fails.

  return [
    'empty'   => TRUE,
    'check'   => fn ( $value ) => in_array ( strtolower ( $value ), [ '1', 'on', 'yes', 'true' ], TRUE ),
    'message' => ':label must be accepted'
  ];

?>
