<?php
    
  if ( ! $pTrace )
    return;

  $pTraceData = [
    'html' => $pHtml [$p]
  ];

  pFile_put_contents ($pOccurDir [$p] . "/end.json", $pTraceData );

  $pOccurDir [$p] = $pLevelDir [$p];

?>