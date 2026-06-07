<?php

  padFilePut ( padFileName (), $padOutput );

  $padSetConfig ['OutputType'] = 'web';

  $padRestart = $padFileNextPage;
  include PAD . 'start/restart.php';

?>