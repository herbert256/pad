<?php

  $padBefore [$pad] = 2;

  padHtml ('');

  $padBeforeData [$pad] = $padResult [$pad+1];

  if ( count ( $padData [$pad] ) ) {
    reset ( $padData [$pad] );
    include PAD . 'pad/occurrence/start.php';
    #dump();
  }

?>