<?php

  pTiming_start ('build');

  if ( isset ( $_REQUEST['pInclude']) )
    $pBuild_mode = 'include';

  include "$pBuild_mode.php";

  if ( $pTrace )
    pFile_put_contents ("$pTraceDir/html-base.html", $pBase [1] );
  
  include PAD . 'level/inits.php';
  include PAD . 'occurrence/start.php';

  pTiming_end ('build');

?>