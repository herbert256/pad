<?php

  $padFile = padFileName ( TRUE );

  padFilePutContents ( $padFile, $padOutput );

  $padSetConfig ['OutputType'] = 'web';

  $padRestart = $padFileNextPage;
  include 'start/enter/restart.php';

?>