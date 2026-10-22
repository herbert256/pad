<?php

  // The page format/a_float_past_the_range_inside_an_object_is_null_in_a_json_answer asks
  // for JSON: INF and NAN inside objects - a cast array, rows as json_decode hands them, an
  // object that serializes itself - where the arrays of format/infinite hold them.

  $obj       = (object) [ 'rate' => INF, 'n' => 1 ];
  $rows      = [ (object) [ 'a' => NAN, 'b' => 1 ], json_decode ( '{"a": -1e400, "b": 2}' ) ];
  $ser       = new class implements JsonSerializable {
                 function jsonSerialize (): mixed { return [ 'x' => INF, 'y' => (object) [ 'z' => NAN ] ]; }
               };
  $padExpose = [ 'obj', 'rows', 'ser' ];

?>
