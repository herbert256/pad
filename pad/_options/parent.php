<?php

  $pad--;

  $padData [$pad] = $padData[$pad+1];
  reset ( $padData [$pad] );
  $padKey [$pad] = key($padData [$pad]);

  $padParentStart = strpos ( $padBase [$pad], '{'.$padTag [$pad]);
  $padParentEnd   = strpos ( $padBase [$pad], "}", $padParentStart) ;

  $padBase [$pad] = substr ( $padBase [$pad], 0, $padParentStart )
                       . substr ( $padBase [$pad], $padParentEnd + 1 );

  $padCurrent [$pad] = [];
  $padOccur   [$pad] = 0;
  $padResult  [$pad] = '';
  $padPad    [$pad] = '';

?>