<?php

  $padBuildMrg = padExplode ( "pages/$padPage", '/' );

  include 'lib.php';  

  if ( ! isset ( $GLOBALS['padIgnoreInclude'] ) )
    if ( $padInclude or isset ( $_REQUEST['padInclude'] ) )
      $padBuildMode = 'include';

  include "$padBuildMode.php";

  include pad . 'occurrence/start.php';

?>