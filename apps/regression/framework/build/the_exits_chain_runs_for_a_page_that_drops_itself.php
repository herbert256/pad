<?php

  // The _exits.php chain runs for a page that drops itself, as for any other: it returned
  // before them while the _exits.pad wrapper still rendered, so the wrapper read a field
  // the skipped _exits.php would have set - a 500 under the strict check.

  $dropCurl = padCurl ( $padGoExt . 'build/a_page_that_drops_itself' );
  $dropSeen = preg_match ( '/\[exits: [a-z]*\]/', $dropCurl ['data'], $dropMatch ) ? $dropMatch [0] : $dropCurl ['result'];

?>
