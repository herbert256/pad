<?php

  $padFile = padFileName ( TRUE );

  padFilePutContents ( $padFile, $padOutput );

  $padSetConfig ['OutputType'] = 'web';

  $padRestart = $padFileNextPage;
  include pad . 'start/enter/restart.php';

?>