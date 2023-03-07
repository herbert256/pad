<?php

  $padTraceData = [
    'store'  => $padStoreName, 
    'entry'  => $padName [$pad],
    'source' => $padStoreSource, 
    'result' => $padStoreData
  ];

  padFilePutContents ( $padLevelDir [$pad] . "/store.json", $padTraceData ); 

?>