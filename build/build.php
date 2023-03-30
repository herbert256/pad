<?php

  $padBuildMrg = padExplode ( "pages/$padPage", '/' );

  include 'lib.php';  

  if ( ! isset ( $GLOBALS['padIgnoreInclude'] ) )
    if ( $padInclude or isset ( $_REQUEST['padInclude'] ) )
      $padBuildMode = 'include';

  include "$padBuildMode.php";

  if ($padParse) include pad . 'parse/build.php';
  if ($padLog)  include pad . 'log/build.php';

  include pad . 'occurrence/start.php';

?>