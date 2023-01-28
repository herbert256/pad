<?php

  if ( isset ( $_REQUEST['padInclude']) )
    $padBuildMode = 'include';

  include "$padBuildMode.php";

  if ( ! isset ( $padNoOccur) )
    include PAD . 'pad/occurrence/start.php';

?>