<?php

  include PAD . 'build/build.php';

  if ( $padTrace )
    padFilePutContents ("$padTraceDir/base.html", $padBase [$pad] );

?>