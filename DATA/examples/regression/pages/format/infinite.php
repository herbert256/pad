<?php

  // The page format/a_float_past_the_range_is_null_in_a_json_answer asks for JSON and CSV: a
  // float overflowed to INF, a row holding one, and a NAN.

  $big       = INF;
  $none      = NAN;
  $rows      = [ [ 'a' => INF, 'b' => 1 ], [ 'a' => -INF, 'b' => 2 ] ];
  $padExpose = [ 'rows', 'big', 'none' ];

?>
