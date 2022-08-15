<?php
    
  if ( ! $padTrace )
    return;

  $padTraceData = [
    'html' => $padHtml [$pad]
  ];

  padFilePutContents ($padOccurDir [$pad] . "/end.json", $padTraceData );

?>