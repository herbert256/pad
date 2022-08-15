<?php

  pTiming_start ('build');

  if ( isset ( $_REQUEST['pInclude']) )
    $padBuild_mode = 'include';

  include "$padBuild_mode.php";

  if ( ! isset ( $padNoOccur) )
    include PAD . 'occurrence/start.php';

  pTiming_end ('build');

?>