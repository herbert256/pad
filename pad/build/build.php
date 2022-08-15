<?php

  pTiming_start ('build');

  if ( isset ( $_REQUEST['pInclude']) )
    $pBuild_mode = 'include';

  include "$pBuild_mode.php";

  if ( ! isset ( $pNoOccur) )
    include PAD . 'occurrence/start.php';

  pTiming_end ('build');

?>