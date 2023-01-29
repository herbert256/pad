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

  include PAD . "pad/options/go/start.php";
  
  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']))
    include PAD . 'pad/callback/init.php' ;

  include 'trace/level.php';

  if ( count ( $padData [$pad] ) )
    include PAD . 'pad/occurrence/start.php';
  
?>