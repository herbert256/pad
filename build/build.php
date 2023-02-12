<?php

  $padBuildMrg = padExplode ( "pages/$page", '/' );

  include 'lib.php';  

  if ( $padInclude or isset ( $_REQUEST['padInclude'] ) )
    $padBuildMode = 'include';

  include "$padBuildMode.php";

  include PAD . 'occurrence/start.php';

?>