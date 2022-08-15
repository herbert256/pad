<?php

  include PAD . 'build/build.php';

  if ( $pTrace )
    pFile_put_contents ("$pTraceDir/base.html", $pBase [$p] );

?>