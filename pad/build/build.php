<?php

  pTiming_start ('build');

  $pBase [$p] = '';

  include "$pBuild_mode.php";

  if ( $pTrace )
    pFile_put_contents ("$pTrace_dir/html-base.html", $pBase [1] );
  
  include PAD . 'occurrence/start.php';

  pTiming_end ('build');

?>