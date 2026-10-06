<?php

  // The error stands in the _inits.pad around the page, which composes on a full fetch
  // only; the report has to name the wrapper, not the page inside it.
  // Read from the text report a browser gets.

  $out = sourceProbe ( 'source/framed/index', 'where', 'Mozilla/5.0' );

?>
