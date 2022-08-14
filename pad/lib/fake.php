<?php

  function pBuild ( $page, $mode='before', $merge='content' ) {

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 1) == 'p' )
        global $$key;

    $Xp         = $p;
    $XpTraceDir = $pTraceDir;
    
    $pCnt++;

    include PAD . 'level/setup.php'; 

    $pTraceDir      = $pOccurDir [$p] . '/build';
    $pLevelDir [$p] = $pTraceDir;
    $pOccurDir [$p] = $pTraceDir;

    include PAD . 'level/trace/start.php'; 
    include PAD . 'occurrence/trace/start.php'; 

    $pBuild_mode  = $mode;
    $pBuild_merge = $merge;

    include PAD . 'build/build.php'; 

    $pHtml [$p] = $Base [$p];    

    while ( $p > $Xp ) 
      include PAD . 'level/level.php'; 

    $pTraceDir = $XpTraceDir;
 
    return $pHtml [$Xp+1];

  }

  function pFakeXXX ( $contentxx ) {

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 1) == 'p' )
        global $$key;

    $Xp = $p;
    
    $pCnt++;

    include PAD . 'level/setup.php'; 
    include PAD . 'level/trace/start.php'; 
    include PAD . 'occurrence/trace/start.php'; 

    $pHtml [$p] = $contentxx;    

    while ( $p > $Xp ) 
      include PAD . 'level/level.php'; 

    return $pHtml [$Xp+1];

  }

?>