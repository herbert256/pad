<?php

  pTiming_start ('build');

  if ( isset ( $_REQUEST['pInclude']) )
    $pBuild_mode = 'include';

  include "$pBuild_mode.php";

  if ( $pTrace )
    pFile_put_contents ("$pTraceDir/base.html", $pBase [$p] );
  
  include PAD . 'occurrence/start.php';

  pTiming_end ('build');

?>