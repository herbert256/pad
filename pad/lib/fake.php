<?php

  function pad_fake ( $tag, $options, $content='', $false='', $kind='open' ) {

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 3) == 'pad' )
        global $$key;

    $fake_lvl       = $pad;
    $fake_timing    = $pad_timing;
    $fake_trace_dir = $pad_trace_dir;
 
    $pad_timing = FALSE;

    $pad_between    = 'fake';
    $pad_type       = 'fake';
    $pad_pair       = FALSE;
    $pad_trace_dir  = $pad_level_dir . '/FAKE'; 
    $pad_level_dir  = $pad_trace_dir;
    $pad_occur_dir  = $pad_trace_dir;

    $pad++; 
    include PAD . 'level/setup.php'; 
    
    $pad++; 
    include PAD . 'level/setup.php'; 

                                       $fake_base  = '{' . "$tag";
    if ($kind == 'open' and $options)  $fake_base .= " $options";
                                       $fake_base .= "}$content{else}$false{/$tag";
    if ($kind == 'close' and $options) $fake_base .= " $options";
                                       $fake_base .= "}";

    $pad_base [$pad] = $fake_base ;

    include PAD . 'occurrence/start.php';

    while ( $pad > $fake_lvl ) 
      include PAD . 'level/level.php'; 

    $return = $pad_html [$pad];

    foreach ( $GLOBALS['pad_parms'] [$fake_lvl] as $fake_key => $fake_val )
      $GLOBALS['pad_'.$fake_key] = $fake_val;

    $pad           = $fake_lvl;
    $pad_timing    = $fake_timing;
    $pad_trace_dir = $fake_trace_dir;

    return $pad_html [$fake_lvl+1];

  }
 
 
  function pad_field_fake_level ( $name, $data ) {

    global $pad;

    $pad_save = $pad;

    $pad = 999999;
    include PAD . 'inits/setup.php';
  
    $GLOBALS ['pad_data']    [$pad] = pad_make_data ( $fake );
    $GLOBALS ['pad_current'] [$pad] = reset ( $GLOBALS ['pad_data'] );
    $GLOBALS ['pad_key']     [$pad_key] = key ( $GLOBALS ['pad_data'] [$pad] );
    $GLOBALS ['pad_occur']   [$pad_key] = 1;

    $pad = $pad_save;

    return 9999;
    
  }

?>