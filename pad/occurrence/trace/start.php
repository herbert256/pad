<?php

  if ( ! $pTrace )
    return;

  $pTraceData = [
    'key'  => $pKey     [$p],
    'data' => $pCurrent [$p],
    'html' => $pHtml    [$p]
  ];

  $pOccurDir [$p] = $pLevelDir [$p] . '/occur-' . $pOccur [$p];

  pFile_put_contents ( $pOccurDir [$p] . "/start.json",     $pTraceData );
  
?>