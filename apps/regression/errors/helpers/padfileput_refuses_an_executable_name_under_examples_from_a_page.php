<?php

  // The examples/ store keeps the harvest's copies of the applications' .php sources, and
  // only the harvest may write one there: a page that names examples/... is held to the rule
  // every other name is, or padFilePut ( 'examples/' . $name ) wrote a page that runs.

  padFilePut ( 'examples/zzaudit/report.php', 'x' );

?>
