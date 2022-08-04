<?php

  function pad_pad ( $tag, $options, $content='', $false='', $kind='open' ) {

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 3) == 'pad' )
        global $$key;

    $fake_lvl    = $pad;
    $fake_timing = $pad_timing;
 
    $pad_timing = FALSE;

    $pad++;
    
    $pad_parms [$pad] = [];
    $pad_data       [$pad] = [];

    $pad_html  [$pad] = '';
    $pad_start [$pad] = 0;
    $pad_end   [$pad] = 0;

    include PAD . 'build/level.php';

    if ($kind == 'open') $fake_base = '{' . $tag . ' ' . $options . '}';
    else                 $fake_base = '{' . $tag . '}';

    $fake_base .= "$content{else}$false";

    if ($kind == 'close') $fake_base .= '{/' . $tag . ' ' . $options . '}';
    else                  $fake_base .= '{/' . $tag . '}';

    $pad_base [$pad] = $fake_base ;

    include PAD . 'occurrence/start.php';

    while ( $pad > $fake_lvl + 1 ) 
      include PAD . 'level/level.php'; 

    $GLOBALS['pad']    = $fake_lvl;
    $GLOBALS['pad_timing'] = $fake_timing;

    foreach ( $GLOBALS['pad_parms'] [$fake_lvl] as $fake_key => $fake_val )
      $GLOBALS['pad_'.$fake_key] = $fake_val;

    return $pad_html [$pad];

  }

  function pad_fake_level ( $between, $data=[], $content='', $false='' ) {

    $level_current = $GLOBALS ['pad'];

    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 3) == 'pad' )
        $$key = $val;

    $pad              = $level_current + 1;
    $pad_html  [$pad] = '{' . $between . '}';
    $pad_start [$pad] = 0;
    $pad_end   [$pad] = strlen($pad_html[$pad]) - 1;

    $pad_between   = $between;
    $pad_pair      = FALSE;
    $pad_content   = $content;
    $pad_false     = $false;

    $pad_fake_data = $data;

    include PAD . 'level/parms1.php';
    include PAD . 'level/type_go.php';
    include PAD . 'level/start.php';
    include PAD . 'level/end.php';

    return $pad_html [$pad];

  }
 
  function pad_tag_as_function ( $type, $between ) {

    $GLOBALS['pad']++;
    $pad = $GLOBALS['pad'];

    $GLOBALS['pad_walks']       [$pad] = '';
    $GLOBALS['pad_current']     [$pad] = [];
    $GLOBALS['pad_base']        [$pad] = '';
    $GLOBALS['pad_occur']       [$pad] = 0;
    $GLOBALS['pad_result']      [$pad] = '';
    $GLOBALS['pad_html']        [$pad] = '';
    $GLOBALS['pad_db']          [$pad] = '';
    $GLOBALS['pad_db_lvl']      [$pad] = [];
    $GLOBALS['pad_save_vars']   [$pad] = [];
    $GLOBALS['pad_delete_vars'] [$pad] = [];

    foreach ($GLOBALS as $key => $value)
      if ( substr($key, 0, 3) == 'pad' )
        $$key = $value;    
  
    $pad_between = $between;
    include PAD . 'level/parms1.php';

    $pad_tag_type     = $type;
    $pad_content      = '';
    $pad_false        = '';
    $pad_pair         = FALSE;
    $pad_name         = $pad_tag;
    $pad_options_done = [];
    $pad_walk         = 'start';

    $pad_data [$pad] [1] = [];

    include PAD . 'level/parms2.php';
    include PAD . "level/type.php";
    include PAD . "level/flags.php";

    $result = $pad_tag_result;

    if ( $pad_walk == 'end' ) {
      $pad_base [$pad] = $pad_html [$pad] = $pad_result [$pad] = $pad_content;
      include PAD . "level/type.php";
      include PAD . "level/flags.php";
    }

    foreach ( $GLOBALS['pad_parms'] [$pad-1] as $pad_k => $pad_v )
      $GLOBALS[$pad_k] = $pad_v;    

    $GLOBALS['pad']--;

    if ( ! pad_is_default_data ( $pad_data [$pad] ) )
      return $pad_data [$pad];
    elseif ( $pad_content !== '' )
      return $pad_content;
    else
      return $result;

  } 


  function pad_field_fake_level ( $name, $data ) {

    global $pad;

    $pad_save = $pad;

    $pad = 999999;
    include PAD . 'inits/level.php';
  
    $GLOBALS ['pad_data']    [$pad] = pad_make_data ( $fake );
    $GLOBALS ['pad_current'] [$pad] = reset ( $GLOBALS ['pad_data'] );
    $GLOBALS ['pad_key']     [$pad_key] = key ( $GLOBALS ['pad_data'] [$pad] );
    $GLOBALS ['pad_occur']   [$pad_key] = 1;

    $pad = $pad_save;

    return $pad;
    
  }

?>