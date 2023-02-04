<?php

  include 'config.php';

  if ( isset ( $_REQUEST['padInclude']) )
    $padBuildMode = 'include';

  include "$padBuildMode.php";
  include 'lib.php';  

  $padBase [$pad] = "{root}{lib}$padLibData{/lib}" . $padBase [$pad] . '{/root}';

  include PAD . 'occurrence/start.php';

?>