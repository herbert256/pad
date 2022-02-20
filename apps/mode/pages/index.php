<?php

  function get_mode2 ($mode, $merge) {

    global $pad_host, $pad_script;

    $input ['url'] = "$pad_host$pad_script?app=mode&page=level1/level2/page&mode=$mode&merge=$merge";
    $input ['cookies'] ['PADSESSID'] = $GLOBALS['PADSESSID'];
    $input ['cookies'] ['PADREQID']  = $GLOBALS['PADREQID'];
    
    return pad_curl ($input, $output);
    
  }

  function get_mode ($mode, $merge) {

    return pad ( "level1/level2/page&mode=$mode&merge=$merge");
    
  }

?>