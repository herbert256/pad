<?php

  if ( ! $padTrace )
    return;

  $padTraceData = [
    'key'  => $padKey     [$pad],
    'data' => $padCurrent [$pad],
    'html' => $padHtml    [$pad]
  ];

  $padOccurDir [$pad] = $padLevelDir [$pad] . '/occur-' . $padOccur [$pad];

  pFile_put_contents ( $padOccurDir [$pad] . "/start.json",     $padTraceData );
  
?>