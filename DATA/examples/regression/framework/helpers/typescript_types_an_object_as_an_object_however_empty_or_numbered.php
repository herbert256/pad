<?php

  // JSON keeps an object apart from a list: an empty row among rows, a list of empty
  // objects and an object keyed 0, 1 were typed as lists - a component called .map on an
  // object, and row.a was refused.

  $value = json_decode ( '{"rows":[{"a":1},{}],"objs":[{}],"zero":{"0":"a","1":"b"},"list":["a","b"]}' );

  $out = padTypeScriptInterface ( 'Empties', $value );

?>
