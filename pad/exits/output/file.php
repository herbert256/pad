<?php

  $padFile = padFileName ( TRUE );

  padFilePutContents ( $padFile, $padOutput );

  $padSetConfig ['OutputType'] = 'web';

  $padRestart = $padFileNextPage;
  include PAD . 'start/enter/restart.php';

?>