<?php

  function pFake ( $tag, $options, $content='', $false='', $kind='open' ) {

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 1) == 'p' )
        global $$key;

    $fake_lvl       = $p;
    $fake_timing    = $pTiming;
    $fake_trace_dir = $pTraceDir;
 
    $pTiming = FALSE;

    include PAD . 'level/setup.php'; 
    $pBetween   = 'fake';
    include PAD . 'level/between.php'; 
    $pType [$p]  = 'true';
    $pPair [$p]  = FALSE;

    include PAD . 'level/setup.php'; 
    $pBetween   = 'fake';
    include PAD . 'level/between.php'; 
    $pType [$p]  = 'true';
    $pPair [$p]  = FALSE;
                                       $fake_base  = '{' . "$tag";
    if ($kind == 'open' and $options)  $fake_base .= " $options";
                                       $fake_base .= "}$content{else}$false{/$tag";
    if ($kind == 'close' and $options) $fake_base .= " $options";
                                       $fake_base .= "}";

    $pBase [$p] = $fake_base ;

    include PAD . 'occurrence/start.php';

    while ( $p > $fake_lvl ) 
      include PAD . 'level/level.php'; 

    $return = $pHtml [$p];

    $p         = $fake_lvl;
    $pTiming   = $fake_timing;
    $pTraceDir = $fake_trace_dir;

    return $pHtml [$fake_lvl+1];

  }
 
 
  function pField_fake_level ( $name, $data ) {

    global $p;

    $pSave = $p;

    $p = 999999;
    include PAD . 'inits/setup.php';
  
    $GLOBALS ['pData']    [$p] = pMake_data ( $fake );
    $GLOBALS ['pCurrent'] [$p] = reset ( $GLOBALS ['pData'] );
    $GLOBALS ['pKey']     [$pKey] = key ( $GLOBALS ['pData'] [$p] );
    $GLOBALS ['pOccur']   [$pKey] = 1;

    $p = $pSave;

    return 9999;
    
  }

?>