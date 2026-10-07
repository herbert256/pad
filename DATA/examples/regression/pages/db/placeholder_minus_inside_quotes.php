<?php

  // The space that keeps a bare negative number from joining a minus into a -- comment
  // belongs outside quotes only: inside a quoted literal the value is text, and 'A-{0}' with
  // -1 is A--1, not A- -1.

  $quoted = db ( "field 'A-{0}'",   [ '-1' ] );
  $bare   = db ( "field 10-{0}",    [ -5 ] );

?>
