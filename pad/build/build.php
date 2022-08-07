<?php

  pTiming_start ('build');

  $pBase[1] = '';

  include "$pBuild_mode.php";

  if ( $pTrace )
    pFile_put_contents ("$pTraceDir/html-base.html", $pBase [1] );
  
  include PAD . 'occurrence/start.php';

  pTiming_end ('build');

?>