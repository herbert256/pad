<?php

  $padCnt++;

  if ( $padTrace )
    include 'trace/start.php';

  include 'inits.php';

  $padContent = $padTrue [$pad];
  include 'go.php';
  $padTrue [$pad] = $padContent;
  
  include 'options.php';
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  include PAD . "pad/options/go/start.php";

  include 'name.php';
  
  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']))
    include PAD . 'pad/callback/init.php' ;

  if ( strpos ( $padBase[$pad], '@end@') !== FALSE )
    include 'split/after1.php';

  if ( strpos ( $padBase[$pad], '@start@') !== FALSE )
    return include 'split/before1.php';

  if ( count ( $padData [$pad] ) )
    include PAD . 'pad/occurrence/start.php';

?>