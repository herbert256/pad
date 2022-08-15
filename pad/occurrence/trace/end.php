<?php
    
  if ( ! $padTrace )
    return;

  $padTraceData = [
    'html' => $padHtml [$pad]
  ];

  pFile_put_contents ($padOccurDir [$pad] . "/end.json", $padTraceData );

?>