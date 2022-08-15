<?php

  
  function pRestart ( $pRestart ) {
      
    $GLOBALS ['pRestart'] = $pRestart;

    return NULL;

  }


  function pBuild ( $pBuild, $pMode='include', $pMerge='content', $pInclude=0 ) {
      
    $pBuild_mode  = $pMode;
    $pBuild_merge = $pMerge;
    $page         = $pBuild;
   
    $pNoOccur = TRUE;
    
    global $p, $pBase;    

    include PAD . 'build/build.php'; 

    return $pBase [$p];    

  }


  function pBuild2 ( $pBuild, $pMode='before', $pMerge='content' ) {

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 1) == 'p' )
        global $$key;

    $Xp         = $p;
    $XpTraceDir = $pTraceDir;
    
    $pCnt++;

    include PAD . 'level/setup.php'; 
    include PAD . 'level/trace/start.php'; 
    include PAD . 'occurrence/trace/start.php'; 

    $pBuild_mode  = $pMode;
    $pBuild_merge = $pMerge;
    $page         = $pBuild;

    include PAD . 'build/build.php'; 

    $pHtml [$p] = $pBase [$p];    

    while ( $p > $Xp ) 
      include PAD . 'level/level.php'; 

    $pTraceDir = $XpTraceDir;
 
    return $pHtml [$Xp+1];

  }


  function pContent ( $contentxx ) {

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