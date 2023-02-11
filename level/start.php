<?php

  $padCnt++;

  if ( $padLog )    include PAD . 'log/level.php';
  if ( $padTrace )  include 'trace/start.php';

  include 'inits.php';

  $padContent = $padTrue [$pad];
  include 'go.php';
  $padTrue [$pad] = $padContent;
  
  include 'options.php';
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  include PAD . "options/go/start.php";

  if ( count ( $padOptionsApp [$pad] ) )
    include PAD . "options/go/app.php";

  include 'name.php';
  
  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include PAD . 'callback/init.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@', 2 ) )
    include 'split/after1.php';

  if ( padOpenCloseOk ( $padBase[$pad], '@start@', 1 ) )
    return include 'split/before1.php';

  if ( count ( $padData [$pad] ) )
    include PAD . 'occurrence/start.php';

?>