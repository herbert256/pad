<?php

  $pad--;

  $padData [$pad] = $padData[$pad+1];
  reset ( $padData [$pad] );
  $padKey [$pad] = key($padData [$pad]);

  $padParent_start = strpos ( $padBase [$pad], '{'.$padTag [$pad]);
  $padParent_end   = strpos ( $padBase [$pad], "}", $padParent_start) ;

  $padBase [$pad] = substr ( $padBase [$pad], 0, $padParent_start )
                       . substr ( $padBase [$pad], $padParent_end + 1 );

  $padCurrent [$pad] = [];
  $padOccur   [$pad] = 0;
  $padResult  [$pad] = '';
  $padHtml    [$pad] = '';

?>