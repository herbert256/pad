<?php

  $padParseResult [1] ['tag']   = 'start';
  $padParseResult [1] ['true']  = str_replace("\n", '', $padBase [$pad]);  
  $padParseLevel [$pad+1]       = 1;  
  $padParseInfo  [$pad+1]       = 'main';
  $padParseCount                = 1;
  $padParseResult [1] ['result'] = str_replace("\n", '', padRetrieveContent ( $padBase [$pad] ));

  include 'stop.php';

?>