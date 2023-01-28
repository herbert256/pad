<?php

  $padCnt++;

  include 'trace/start.php';
  include 'parms.php';
  include 'inits.php';

  $padContent = $padTrue [$pad];
  include 'go.php';
  $padTrue [$pad] = $padContent;
  
  include 'options.php';
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  include PAD . "options/go/start.php";
  
  if ( isset($padPrmsTag [$pad] ['callback']) and ! isset($padPrmsTag [$pad] ['before']))
    include PAD . 'pad/callback/init.php' ;

  include 'trace/level.php';

  if ( count ( $padData [$pad] ) )
    include PAD . 'pad/occurrence/start.php';
  
?>