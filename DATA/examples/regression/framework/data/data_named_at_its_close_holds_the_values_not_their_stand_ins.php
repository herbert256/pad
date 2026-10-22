<?php

  // A {data} named on its closing tag parses its walked content, in which a value's
  // syntax characters travel as stand-ins - a spliced comma splits no CSV cell, a quote
  // closes no JSON string. The parsed fields kept them: "Smith, John" was stored with the
  // comma's stand-in, so {if}, where= and php: calls compared and measured another string.

  $people = [ [ 'name' => 'Smith, John' ], [ 'name' => 'x=1' ], [ 'name' => 'say "hi"' ] ];

?>
