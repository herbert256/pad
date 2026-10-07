<?php

  // Holding custom elements out of Tidy (padTidyHold, lib/tidy.php) takes one pass over the
  // page for each element name, whatever closes: every <x-icon name="a"/> written as a
  // self-closing tag, and every <x-tip> left open, had the whole rest of the page scanned
  // for its close - 8000 of them took seconds, the time of a page growing with its square.
  // The icons come back as they went in; what Tidy makes of a <x-tip> never closed is its
  // own repair.

  include PAD . 'config/tidy.php';

  $tidyRows = str_repeat ( '<p>row <x-icon name="a"/> text <x-tip>tip</p>', 4000 );

  $tidyStart = microtime ( TRUE );
  $tidyOut   = padTidy ( "<!DOCTYPE html><html><head><title></title></head><body>$tidyRows</body></html>" );
  $tidyTime  = microtime ( TRUE ) - $tidyStart;

  $tidyResult = 'icons: ' . substr_count ( $tidyOut, '<x-icon name="a"' )
              . ', in time: ' . ( $tidyTime < 2 ? 'yes' : sprintf ( 'NO (%.1f s)', $tidyTime ) );

?>
