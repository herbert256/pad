<?php

  // A page the router cannot reach - in a directory with a dot in its name, v1.0/old, or
  // with one in its own name, my.page - answers 404 to every address, and the walk of every
  // page (padAppsList, _common) listed it: develop/?nuts ended on the {ajax} refusing it, a
  // 500 for the whole page. The two are made for the moment of one fetch and taken away
  // again, so no other walk of the tree meets them.

  $nutsDir  = APP . 'develop/nuts.v1.0';
  $nutsFile = APP . 'develop/nuts.dotted.pad';

  @mkdir ( $nutsDir );
  file_put_contents ( "$nutsDir/old.pad", "an old page\n" );
  file_put_contents ( $nutsFile, "a dotted page\n" );

  $nuts = padCurl ( $padHost . 'develop/?nuts&padInclude' );

  unlink ( "$nutsDir/old.pad" );
  rmdir ( $nutsDir );
  unlink ( $nutsFile );

  $answer = $nuts ['result'] . ' - '
          . ( ( str_contains ( $nuts ['data'], 'nuts.v1.0' ) or str_contains ( $nuts ['data'], 'nuts.dotted' ) ) ? 'listed' : 'left out' );

?>
