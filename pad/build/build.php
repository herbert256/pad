<?php

  padTimingStart ('build');

  if ( isset ( $_REQUEST['padInclude']) )
    $padBuildMode = 'include';

  include "$padBuildMode.php";

  if ( ! isset ( $padNoOccur) )
    include PAD . 'occurrence/start.php';

  padTimingEnd ('build');

?>