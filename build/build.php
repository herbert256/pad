<?php

  $padBuildMrg = padExplode ( "pages/$page", '/' );

  include 'lib.php';  

  if ( ! isset ( $GLOBALS['padIgnoreInclude'] ) )
    if ( $padInclude or isset ( $_REQUEST['padInclude'] ) )
      $padBuildMode = 'include';

  include "$padBuildMode.php";

  if ($padParse) include PAD . 'parse/build.php';
  if ($padLog)  include PAD . 'log/build.php';

  include PAD . 'occurrence/start.php';

?>