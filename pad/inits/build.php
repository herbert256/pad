<?php

  include PAD . 'pad/build/build.php';

  if ( $padTrace )
    padFilePutContents ("$padTraceDir/base.html", $padBase [$pad] );

?>