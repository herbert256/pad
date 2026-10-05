<?php

  // The subdirectory's own output hook: it wins over the root's for the pages in here.

  $output = str_replace ( '[[output-hook]]', 'the output hook of sub/ ran', $output );

?>
