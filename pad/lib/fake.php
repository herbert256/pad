<?php

  function pFake ( $tag, $options, $content='', $false='', $kind='open' ) {

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 3) == 'pad' )
        global $$key;

    $fake_lvl       = $pad;
    $fake_timing    = $pTiming;
    $fake_trace_dir = $pTrace_dir;
 
    $pTiming = FALSE;

    $pBetween    = 'fake';
    $pType       = 'fake';
    $pPair       = FALSE;
    $pTrace_dir  = $pLevelDir . '/FAKE'; 
    $pLevelDir  = $pTrace_dir;
    $pOccurDir  = $pTrace_dir;

    $pad++; 
    include PAD . 'level/setup.php'; 
    
    $pad++; 
    include PAD . 'level/setup.php'; 

                                       $fake_base  = '{' . "$tag";
    if ($kind == 'open' and $options)  $fake_base .= " $options";
                                       $fake_base .= "}$content{else}$false{/$tag";
    if ($kind == 'close' and $options) $fake_base .= " $options";
                                       $fake_base .= "}";

    $pBase [$p] = $fake_base ;

    include PAD . 'occurrence/start.php';

    while ( $pad > $fake_lvl ) 
      include PAD . 'level/level.php'; 

    $return = $pHtml [$p];

    foreach ( $GLOBALS['pParms'] [$fake_lvl] as $fake_key => $fake_val )
      $GLOBALS['pad_'.$fake_key] = $fake_val;

    $pad           = $fake_lvl;
    $pTiming    = $fake_timing;
    $pTrace_dir = $fake_trace_dir;

    return $pHtml [$fake_lvl+1];

  }
 
 
  function pField_fake_level ( $name, $data ) {

    global $pad;

    $pSave = $pad;

    $pad = 999999;
    include PAD . 'inits/setup.php';
  
    $GLOBALS ['pData']    [$p] = pMake_data ( $fake );
    $GLOBALS ['pCurrent'] [$p] = reset ( $GLOBALS ['pData'] );
    $GLOBALS ['pKey']     [$pKey] = key ( $GLOBALS ['pData'] [$p] );
    $GLOBALS ['pOccur']   [$pKey] = 1;

    $pad = $pSave;

    return 9999;
    
  }

?>