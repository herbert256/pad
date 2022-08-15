<?php

  include PAD . 'build/build.php';

  if ( $padTrace )
    pFile_put_contents ("$padTraceDir/base.html", $padBase [$pad] );

?>