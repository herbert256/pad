<?php

  // The wrappers compose on a full fetch only - the suite fetches its pages bare - so the
  // framed page is fetched here, and what its error report says wraps it is shown: the
  // JSON report for a tool, the text report for anybody else.
  // Read from the JSON report a tool gets.

  $out = sourceProbe ( 'source/frame/index', 'wrapped', 'curl/8' );

?>
