<?php

  
  function padRestart ( $padRestart ) {
      
    $GLOBALS ['padRestart'] = $padRestart;

    return NULL;

  }


  function padBuild ( $padBuild, $padMode='include', $padMerge='content', $padInclude=0 ) {
      
    $padBuildMode  = $padMode;
    $padBuildMerge = $padMerge;
    $page         = $padBuild;
   
    $padNoOccur = TRUE;
    
    global $pad, $padBase;    

    include PAD . 'build/build.php'; 

    return $padBase [$pad];    

  }


  function padBuild2 ( $padBuild, $padMode='before', $padMerge='content' ) {

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 3) == 'pad' )
        global $$key;

    $Xp         = $pad;
    $XpadTraceDir = $padTraceDir;
    
    $padCnt++;

    include PAD . 'level/setup.php'; 
    include PAD . 'level/trace/start.php'; 
    include PAD . 'occurrence/trace/start.php'; 

    $padBuildMode  = $padMode;
    $padBuildMerge = $padMerge;
    $page         = $padBuild;

    include PAD . 'build/build.php'; 

    $padHtml [$pad] = $padBase [$pad];    

    while ( $pad > $Xp ) 
      include PAD . 'level/level.php'; 

    $padTraceDir = $XpadTraceDir;
 
    return $padHtml [$Xp+1];

  }


  function padContent ( $contentxx ) {

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 3) == 'pad' )
        global $$key;

    $Xp = $pad;
    
    $padCnt++;

    include PAD . 'level/setup.php'; 
    include PAD . 'level/trace/start.php'; 
    include PAD . 'occurrence/trace/start.php'; 

    $padHtml [$pad] = $contentxx;    

    while ( $pad > $Xp ) 
      include PAD . 'level/level.php'; 

    return $padHtml [$Xp+1];

  }


?>