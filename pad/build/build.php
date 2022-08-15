<?php

  padTimingStart ('build');

  if ( isset ( $_REQUEST['padInclude']) )
    $padBuild_mode = 'include';

  include "$padBuild_mode.php";

  if ( ! isset ( $padNoOccur) )
    include PAD . 'occurrence/start.php';

  padTimingEnd ('build');

?>