<?php

  $title = 'PAD configuration settings: $pad_build_mode & $pad_build_merge';
  
  function get_mode ($mode, $merge) {

    global $pad_host, $pad_script;

    $input ['url'] = "$pad_host$pad_script?app=mode&page=level1/level2/page&mode=$mode&merge=$merge";
    $input ['cookies'] ['PADSESSID'] = $GLOBALS['PADSESSID'];
    $input ['cookies'] ['PADREQID']  = $GLOBALS['PADREQID'];

    if ( $GLOBALS['pad_trace'] )
      $input ['url'] .= '&pad_trace=1';

    return pad_curl ($input, $output);
    
  }

?>