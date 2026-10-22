<?php

  // A list wrapped in one named field - {"results": [...]}, the answer of most JSON APIs -
  // is unwrapped to its rows, and an empty one to none: it stayed the wrapper, one row with
  // no fields, so the @else@ never showed and {$n} in it ended the page "not found".

  $res = [ 'results' => [] ];

?>
